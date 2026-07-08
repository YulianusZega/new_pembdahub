import json
import re

# Load Mappings
teachers_db = json.load(open('db_teachers.json'))
subjects_db = json.load(open('db_subjects.json'))
slots_map = json.load(open('slot_mapping.json'))
class_ids = [163, 188, 166, 167, 189, 190, 170, 171, 191, 192, 193, 194, 173, 174, 175, 176, 178, 179, 180, 181, 182, 183, 186, 187]

teacher_names = {
    1: "Agustiani", 2: "Ninisadarwati", 3: "Noverius", 4: "Peniel", 5: "Firwanus",
    6: "Eliasa", 7: "Wijha", 8: "Efiyanti", 9: "Tonaaro", 10: "Yaitolo",
    11: "Otiani", 12: "Filiaro", 13: "Sondrazatulo", 14: "Yeremia", 15: "Markus",
    16: "Agusman", 17: "Martperan", 18: "Desman", 19: "Herman", 20: "Adiyusu",
    21: "Resman", 22: "Sabar", 23: "Solidarman", 24: "Darius", 25: "Yulianus",
    26: "Exaudi", 27: "Defelinu", 28: "Fidel", 29: "Fider", 30: "Lisa",
    31: "Oferius", 32: "Julianus", 33: "Devi", 34: "Hilda", 35: "Elven",
    36: "Yelfi", 37: "Nofika", 38: "Arlika", 39: "Yamonaha", 40: "Sozanolo",
    41: "Immeldha", 42: "Feberina", 43: "Ester", 44: "Eldasari", 45: "Rian"
}

subject_map = {
    'SBD': 214, 'AGAMA': 212, 'PKN': 209, 'PPKN': 209, 'MTK': 207, 'B.INDO': 208,
    'B.ING': 81, 'PJOK': 82, 'PIPAS': 225, 'INFOR': 215, 'INFO': 215, 'SEJR': 213,
    'SEJ': 213, 'MULOK': 217, 'KIK': 223, 'PKK': 223, 'DDPKTO': 218, 'DDPKTE': 218,
    'DDPKDP': 218, 'KKDPIB': 222, 'KKTE': 219, 'KKTKR': 220, 'KKTSM': 221, 'KKTKJ': 222, 
    'MPPTO': 225, 'MPPTE': 225, 'MPPDPIB': 225, 'MPPTJKT': 225, 'KODING': 226, 'KKA': 226, 
    'PKL': 224, 'DDPKTKJ': 222, 'KK-TE': 219, 'KK-TKR': 220, 'DDPK-TO': 218, 'MPP-TO': 225,
    'KK-DPIB': 222, 'DDPK-TE': 218, 'DDPK-DP': 218, 'MPP-TE': 225, 'MPPTKJ': 225, 'KODING-KAI': 226
}

def get_sid(a):
    return subject_map.get(a.upper().replace('.', '').strip(), 225)

def get_tid(n):
    if not n: return None
    try: n = int(n)
    except: return None
    t = teacher_names.get(n, "").lower()
    for tr in teachers_db:
        if t in tr['full_name'].lower(): return tr['id']
    return None

def parse_row(line):
    # Regex for timestamp and Jam Num
    m = re.match(r'(\d+[:.]\d+)\s*[-]\s*\d+[:.]\d+\s+(\d+)\s+(.+)', line)
    if not m: return None
    
    jam = m.group(2)
    content = m.group(3)
    tokens = content.split()
    
    cells = []
    ptr = 0
    while ptr < len(tokens) and len(cells) < 24:
        tok = tokens[ptr]
        sid = get_sid(tok)
        # Check if next is teacher non-jam marker
        if ptr + 1 < len(tokens) and tokens[ptr+1].isdigit():
            # If next is > 11 or if next-next is a subject
            tid = get_tid(tokens[ptr+1])
            cells.append({'s': sid, 't': tid})
            ptr += 2
        else:
            cells.append({'s': sid, 't': None})
            ptr += 1
    return {'jam': jam, 'cells': cells}

def main():
    with open('raw_text.txt', encoding='ascii') as f:
        lines = f.readlines()
        
    all_rows = []
    for l in lines:
        row = parse_row(l.strip())
        if row:
            all_rows.append(row)
            
    print(f"Total rows parsed: {len(all_rows)}")
    
    # Identify Blocks (Days)
    # Block 1: Senin (Starts with 2)
    # Block 2: Selasa (Starts with 1)
    # Block 3: Rabu (Starts with 1)
    # Block 4: Kamis (Starts with 1)
    # Block 5: Jumat (Starts with 1)
    
    day_blocks = []
    curr = []
    prev_jam = 99
    for r in all_rows:
        j = int(r['jam'])
        if j <= prev_jam and curr:
            day_blocks.append(curr)
            curr = []
        curr.append(r)
        prev_jam = j
    if curr: day_blocks.append(curr)
    
    print(f"Blocks found: {len(day_blocks)}")
    day_names = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday']
    
    schedules = []
    for d_idx, d_rows in enumerate(day_blocks):
        if d_idx >= 5: break
        dname = day_names[d_idx]
        print(f"Processing {dname}...")
        for r in d_rows:
            jam = str(r['jam'])
            for ci, cdata in enumerate(r['cells']):
                cid = class_ids[ci]
                slid = slots_map.get(dname, {}).get(jam)
                sid = cdata['s']
                tid = cdata['t']
                
                if sid == 212 and not tid:
                    if ci < 8: tid = get_tid(31)
                    elif ci < 16: tid = get_tid(7)
                    else: tid = get_tid(6)

                if cid and slid and sid:
                    schedules.append({
                        'day': dname, 'slot_id': slid, 'classroom_id': cid,
                        'teacher_id': tid or 1, 'subject_id': sid,
                        'group_key': f"{dname}_{jam}_{tid or 1}_{sid}"
                    })

    # Group Codes
    counts = {}
    for s in schedules: counts[s['group_key']] = counts.get(s['group_key'], 0) + 1
    for s in schedules:
        s['group_code'] = f"GAB-{s['day'][:3].upper()}-{s['slot_id']}-{s['teacher_id']}" if counts[s['group_key']] > 1 else None

    with open('schedules_to_import.json', 'w') as f:
        json.dump(schedules, f)
    print(f"Final Count: {len(schedules)}")

main()
