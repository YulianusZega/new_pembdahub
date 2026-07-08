import json
import sys

# Set stdout to output utf-8
sys.stdout.reconfigure(encoding='utf-8')

log_path = r'C:\Users\ACER\.gemini\antigravity\brain\9960cf82-c3ef-487d-b7fe-714067812970\.system_generated\logs\transcript.jsonl'

with open(log_path, 'r', encoding='utf-8') as f:
    for line in f:
        data = json.loads(line)
        source = data.get('source')
        mtype = data.get('type')
        if mtype in ['USER_INPUT', 'PLANNER_RESPONSE'] or (source == 'MODEL' and mtype == 'TEXT'):
            print('='*80)
            print(f'Source: {source} | Type: {mtype}')
            print('='*80)
            content = data.get('content', '')
            print(content)
            print('\n')
