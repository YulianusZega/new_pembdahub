import json
import re

class_ids = [163, 188, 166, 167, 189, 190, 170, 171, 191, 192, 193, 194, 173, 174, 175, 176, 178, 179, 180, 181, 182, 183, 186, 187]
teachers_db = json.load(open('db_teachers.json'))
subjects_db = json.load(open('db_subjects.json'))
slots_map = json.load(open('slot_mapping.json'))

teacher_names = {i: n for i, n in enumerate(["Agustiani", "Ninisadarwati", "Noverius", "Peniel", "Firwanus", "Eliasa", "Wijha", "Efiyanti", "Tonaaro", "Ya'itolo", "Otiani", "Filiaro", "Sondrazatulo", "Yeremia", "Markus", "Agusman", "Martperan", "Desman", "Herman", "Adiyusu", "Resman", "Sabar", "Solidarman", "Darius", "Yulianus", "Exaudi", "Defelinu", "Fidel", "Fider", "Lisa", "Oferius", "Julianus", "Devi", "Hilda", "Elven", "Yelfi", "Nofika", "Arlika", "Yamonaha", "Sozanolo", "Immeldha", "Feberina", "Ester", "Eldasari", "Rian"], 1)}
teacher_names[46] = "Peniel"

def get_sid(a):
    a = a.upper().replace('.', '').replace('-', '').strip()
    m = {'SBD': 214, 'AGAMA': 212, 'PKN': 209, 'PPKN': 209, 'MTK': 207, 'BINDO': 208, 'BING': 81, 'PJOK': 82, 'PIPAS': 225, 'INFOR': 215, 'INFO': 215, 'SEJR': 213, 'SEJ': 213, 'MULOK': 217, 'KIK': 223, 'PKK': 223, 'DDPKTO': 218, 'DDPKTE': 218, 'DDPKDP': 218, 'DDPKTKJ': 222, 'KKDPIB': 222, 'KKTE': 219, 'KKTKR': 220, 'KKTSM': 221, 'KKTKJ': 222, 'PKL': 224, 'KODING': 226, 'KKA': 226, 'MPPTO': 225, 'MPPTE': 225}
    return m.get(a, 225)

def get_tid(n):
    if not n: return None
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
        m = re.match(r'(\d+[:.]\d+)\s*[-]\s*\d+[:.]\d+\s+(\d+)\s+(.+)', l.strip())
        if not m: continue
        jam = m.group(2); content = m.group(3); tokens = content.split()
        if idx < 18: d = 'monday'
        elif idx < 33: d = 'tuesday'
        else: d = 'friday' if idx > 111 else ('thursday' if idx > 90 else 'wednesday')
            
        ptr = 0
        while ptr < len(tokens):
            s = tokens[ptr]
            if ptr+1 < len(tokens) and tokens[ptr+1].isdigit():
                grid[d][jam].append({'s': get_sid(s), 't': get_tid(tokens[ptr+1]), 'rs': s, 'rt': tokens[ptr+1]})
                ptr += 2
            else:
                grid[d][jam].append({'s': get_sid(s), 't': None, 'rs': s, 'rt': None})
                ptr += 1

    final = []
    log = []
    for dname, hours in grid.items():
        for jam, units in hours.items():
            slid = slots_map.get(dname, {}).get(jam)
            if not slid: continue
            log.append(f"{dname} Jam {jam}: {len(units)} units found")
            for ci, cdata in enumerate(units[:24]):
                cid = class_ids[ci]
                sid = cdata['s']; tid = cdata['t']
                if sid == 212 and not tid:
                    if ci < 8: tid = get_tid(31)
                    elif ci < 16: tid = get_tid(7)
                    else: tid = get_tid(6)
                final.append({'day': dname, 'slot_id': slid, 'classroom_id': cid, 'teacher_id': tid or 1, 'subject_id': sid, 'group_key': f"{dname}_{jam}_{tid or 1}_{sid}"})

    with open('parse_log.txt', 'w') as f: f.write("\n".join(log))
    with open('schedules_to_import.json', 'w') as f: json.dump(final, f)
    print(f"Final Count: {len(final)}")

main()
