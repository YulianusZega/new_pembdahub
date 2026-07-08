import json
import re

# Precise Order for School 3
class_ids = [163, 188, 166, 167, 189, 190, 170, 171, 191, 192, 193, 194, 173, 174, 175, 176, 178, 179, 180, 181, 182, 183, 186, 187]

teachers_db = json.load(open('db_teachers.json'))
subjects_db = json.load(open('db_subjects.json'))
slots_map = json.load(open('slot_mapping.json'))

teacher_names = {1:"Agustiani", 2:"Ninisadarwati", 3:"Noverius", 4:"Peniel", 5:"Firwanus", 6:"Eliasa", 7:"Wijha", 8:"Efiyanti", 9:"Tonaaro", 10:"Yaitolo", 11:"Otiani", 12:"Filiaro", 13:"Sondrazatulo", 14:"Yeremia", 15:"Markus", 16:"Agusman", 17:"Martperan", 18:"Desman", 19:"Herman", 20:"Adiyusu", 21:"Resman", 22:"Sabar", 23:"Solidarman", 24:"Darius", 25:"Yulianus", 26:"Exaudi", 27:"Defelinu", 28:"Fidel", 29:"Fider", 30:"Lisa", 31:"Oferius", 32:"Julianus", 33:"Devi", 34:"Hilda", 35:"Elven", 36:"Yelfi", 37:"Nofika", 38:"Arlika", 39:"Yamonaha", 40:"Sozanolo", 41:"Immeldha", 42:"Feberina", 43:"Ester", 44:"Eldasari", 45:"Rian", 46: "Peniel"} # Added 46 as duplicate of 4 or similar if found

subject_map = {
    'SBD': 214, 'AGAMA': 212, 'PKN': 209, 'PPKN': 209, 'MTK': 207, 'B.INDO': 208,
    'B.ING': 81, 'PJOK': 82, 'PIPAS': 225, 'INFOR': 215, 'INFO': 215, 'SEJR': 213,
    'SEJ': 213, 'MULOK': 217, 'KIK': 223, 'PKK': 223, 'DDPKTO': 218, 'DDPKTE': 218,
    'DDPKDP': 218, 'KKDPIB': 222, 'KKTE': 219, 'KKTKR': 220, 'KKTSM': 221, 'KKTKJ': 222, 
    'MPPTO': 225, 'MPPTE': 225, 'MPPDPIB': 225, 'MPPTJKT': 225, 'KODING': 226, 'KKA': 226, 
    'PKL': 224, 'DDPKTKJ': 222, 'KK-TE': 219, 'KK-TKR': 220, 'DDPK-TO': 218, 'MPP-TO': 225,
    'KK-DPIB': 222, 'DDPK-TE': 218, 'DDPK-DP': 218, 'MPP-TE': 225, 'MPPTKJ': 225, 'KODING-KAI': 226,
    'MPPDPIB': 225, 'MPPTE': 225, 'KK-TSM': 221
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

def main():
    with open('raw_text.txt', encoding='ascii') as f:
        lines = f.readlines()
        
    # Schedule Grid: 5 days x 11 hours x 24 classes
    # grid[day][jam][class_idx] = {'s': id, 't': id}
    grid = {d: {str(j): [None]*24 for j in range(1, 12)} for d in ['monday', 'tuesday', 'wednesday', 'thursday', 'friday']}
    
    current_day = 'monday'
    last_jam = 0
    
    # Heuristic Day Detection based on line sequences in raw_text.txt
    # Line 1-17: Monday (ish)
    # Line 18-32: Tuesday
    # Line 33-47: Wednesday
    # Line 93-110: Thursday
    # Line 111-123: Friday
    
    for idx, l in enumerate(lines):
        l = l.strip()
        if not l: continue
        
        # Day Marker Check
        if 'SENIN' in l: current_day = 'monday'
        elif 'SELASA' in l: current_day = 'tuesday'
        elif 'RABU' in l: current_day = 'wednesday'
        elif 'KAMIS' in l: current_day = 'thursday'
        elif 'JUMAT' in l: current_day = 'friday'
        
        # Row Detect
        m = re.match(r'(\d+[:.]\d+)\s*[-]\s*\d+[:.]\d+\s+(\d+)\s+(.+)', l)
        if not m:
            # Maybe it's a row WITHOUT a Jam number (e.g. some religions)
            m2 = re.match(r'(\d+[:.]\d+)\s*[-]\s*\d+[:.]\d+\s+(.+)', l)
            if m2:
                # Use current_day and last_jam? No, wait. 
                # Better to just look at the line number blocks.
                pass
            continue
            
        jam = m.group(2)
        content = m.group(3)
        tokens = content.split()
        
        # Assign Day by Line Number if not explicitly detected
        if idx < 18: d = 'monday'
        elif idx < 33: d = 'tuesday'
        elif idx < 50: d = 'wednesday'
        elif idx < 111: d = 'thursday'
        else: d = 'friday'
            
        # Parse Tokens into 24 slots
        # If we have 24*2 = 48 tokens? No.
        # But look at Jam 1/2 for Thursday...
        # Line 98: 1 B.INDO 34 B.INDO 34 ... (Total 48 tokens?)
        # Let's consume them properly.
        ptr = 0
        cells = []
        while ptr < len(tokens):
            s_abbr = tokens[ptr]
            sid = get_sid(s_abbr)
            if ptr + 1 < len(tokens) and tokens[ptr+1].isdigit():
                tid = get_tid(tokens[ptr+1])
                cells.append({'s': sid, 't': tid})
                ptr += 2
            else:
                cells.append({'s': sid, 't': None})
                ptr += 1
                
        # Update grid with these cells (starting from 0)
        # Some rows might only have a few cells (e.g. Religion at the end)
        # If cells count < 24? 
        # Actually, look at Wednesday Jam 8 (Line 14 and 29). 
        # Line 14 has 8 units, Line 29 has 15 units.
        # This is a mess. 
        # But usually, it's 24 in a row.
        
        # If we have 24, just fill.
        if len(cells) == 24:
            grid[d][jam] = cells
        elif len(cells) > 0:
            # Partial row? Store it? 
            # For now, let's just append and take 24.
            # Grid[d][jam] might be partially filled.
            for i, c in enumerate(cells):
                if i < 24:
                    if grid[d][jam][i] is None or c['t'] is not None:
                         grid[d][jam][i] = c

    # Convert Grid to Schedules
    final = []
    for dname, d_data in grid.items():
        for jam, row in d_data.items():
            slid = slots_map.get(dname, {}).get(jam)
            if not slid: continue
            
            for ci, cdata in enumerate(row):
                if cdata:
                    cid = class_ids[ci]
                    sid = cdata['s']
                    tid = cdata['t']
                    
                    # Religion default
                    if sid == 212 and not tid:
                        if ci < 8: tid = get_tid(31)
                        elif ci < 16: tid = get_tid(7)
                        else: tid = get_tid(6)
                    
                    if sid:
                        final.append({
                            'day': dname, 'slot_id': slid, 'classroom_id': cid,
                            'teacher_id': tid or 1, 'subject_id': sid,
                            'group_key': f"{dname}_{jam}_{tid or 1}_{sid}"
                        })

    # Group Codes
    counts = {}
    for s in final: counts[s['group_key']] = counts.get(s['group_key'], 0) + 1
    for s in final:
        s['group_code'] = f"GAB-{s['day'][:3].upper()}-{s['slot_id']}-{s['teacher_id']}" if counts[s['group_key']] > 1 else None

    with open('schedules_to_import.json', 'w') as f:
        json.dump(final, f)
    print(f"Final Count: {len(final)}")

main()
