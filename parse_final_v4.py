import json
import re

class_ids = [163, 188, 166, 167, 189, 190, 170, 171, 191, 192, 193, 194, 173, 174, 175, 176, 178, 179, 180, 181, 182, 183, 186, 187]
teachers_db = json.load(open('db_teachers.json'))
subjects_db = json.load(open('db_subjects.json'))
slots_map = json.load(open('slot_mapping.json'))

teacher_names = {i: n for i, n in enumerate(["Agustiani", "Ninisadarwati", "Noverius", "Peniel", "Firwanus", "Eliasa", "Wijha", "Efiyanti", "Tonaaro", "Ya'itolo", "Otiani", "Filiaro", "Sondrazatulo", "Yeremia", "Markus", "Agusman", "Martperan", "Desman", "Herman", "Adiyusu", "Resman", "Sabar", "Solidarman", "Darius", "Yulianus", "Exaudi", "Defelinu", "Fidel", "Fider", "Lisa", "Oferius", "Julianus", "Devi", "Hilda", "Elven", "Yelfi", "Nofika", "Arlika", "Yamonaha", "Sozanolo", "Immeldha", "Feberina", "Ester", "Eldasari", "Rian"], 1)}
teacher_names[46] = "Peniel"

subject_map = {
    'SBD': 214, 'AGAMA': 212, 'PKN': 209, 'PPKN': 209, 'MTK': 207, 'B.INDO': 208, 'BINDO': 208,
    'B.ING': 81, 'BING': 81, 'PJOK': 82, 'PIPAS': 225, 'INFOR': 215, 'INFO': 215, 'SEJR': 213,
    'SEJ': 213, 'MULOK': 217, 'KIK': 223, 'PKK': 223, 'DDPKTO': 218, 'DDPKTE': 218,
    'DDPKDP': 218, 'KKDPIB': 222, 'KKTE': 219, 'KKTKR': 220, 'KKTSM': 221, 'KKTKJ': 222, 
    'MPPTO': 225, 'MPPTE': 225, 'MPPDPIB': 225, 'MPPTJKT': 225, 'KODING': 226, 'KKA': 226, 
    'PKL': 224, 'DDPKTKJ': 222, 'KK-TE': 219, 'KK-TKR': 220, 'DDPK-TO': 218, 'MPP-TO': 225,
    'KK-DPIB': 222, 'DDPK-TE': 218, 'DDPK-DP': 218, 'MPP-TE': 225, 'MPPTKJ': 225, 'KODING-KAI': 226,
    'KK-TSM': 221, 'SENI': 214
}

def get_sid(a): return subject_map.get(a.upper().replace('.', '').replace('-', '').strip(), 225)
def get_tid(n):
    if not n or n == 'NG': return None
    try: n = int(n)
    except: return None
    t = teacher_names.get(n, "").lower()
    for tr in teachers_db:
        if t in tr['full_name'].lower(): return tr['id']
    return None

def main():
    with open('raw_text.txt', encoding='ascii') as f: lines = f.readlines()
    
    grid = {d: {str(j): [] for j in range(1, 12)} for d in ['monday', 'tuesday', 'wednesday', 'thursday', 'friday']}
    
    for idx, l in enumerate(lines):
        l = l.strip()
        if not l: continue
        
        m = re.match(r'(\d+[:.]\d+)\s*[-]\s*\d+[:.]\d+\s+(\d+)\s+(.+)', l)
        if not m: continue
            
        jam = m.group(2); content = m.group(3); tokens = content.split()
        
        # Day Anchor
        # Monday (P13, top): lines 1-17
        # Tuesday (P13, mid): lines 18-32
        # Wednesday (P13, bottom): lines 33-50
        # Thursday (P14, top): lines 93-110
        # Friday (P14, mid): lines 111-123
        if idx < 18: d = 'monday'
        elif idx < 33: d = 'tuesday'
        elif idx < 50: d = 'wednesday'
        elif idx < 111: d = 'thursday'
        else: d = 'friday'
            
        ptr = 0
        while ptr < len(tokens):
            s_abbr = tokens[ptr]
            if s_abbr == 'NG': ptr += 1; continue
            sid = get_sid(s_abbr)
            if ptr + 1 < len(tokens) and tokens[ptr+1].isdigit():
                grid[d][jam].append({'s': sid, 't': get_tid(tokens[ptr+1])})
                ptr += 2
            else:
                grid[d][jam].append({'s': sid, 't': None})
                ptr += 1

    final = []
    for dname, hours in grid.items():
        for jam, units in hours.items():
            slid = slots_map.get(dname, {}).get(jam)
            if not slid: continue
            
            # Use units collected, up to 24 slots. 
            # If multiple rows for same Jam (Page split), they are already appended.
            for ci, cdata in enumerate(units[:24]):
                cid = class_ids[ci]
                sid = cdata['s']; tid = cdata['t']
                if sid == 212 and not tid:
                    if ci < 8: tid = get_tid(31)
                    elif ci < 16: tid = get_tid(7)
                    else: tid = get_tid(6)
                
                final.append({'day': dname, 'slot_id': slid, 'classroom_id': cid, 'teacher_id': tid or 1, 'subject_id': sid, 'group_key': f"{dname}_{jam}_{tid or 1}_{sid}"})

    counts = {}
    for s in final: counts[s['group_key']] = counts.get(s['group_key'], 0) + 1
    for s in final:
        s['group_code'] = f"GAB-{s['day'][:3].upper()}-{s['slot_id']}-{s['teacher_id']}" if counts[s['group_key']] > 1 else None

    with open('schedules_to_import.json', 'w') as f: json.dump(final, f)
    print(f"Final Count: {len(final)}")

main()
