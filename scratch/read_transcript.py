import json
import sys

sys.stdout.reconfigure(encoding='utf-8')

def print_conv(conv_id):
    path = f"C:\\Users\\ACER\\.gemini\\antigravity\\brain\\{conv_id}\\.system_generated\\logs\\transcript.jsonl"
    print(f"=== CONVERSATION {conv_id} ===")
    try:
        with open(path, 'r', encoding='utf-8') as f:
            for line in f:
                data = json.loads(line)
                step = data.get('step_index')
                t = data.get('type')
                content = data.get('content', '')
                if t == 'USER_INPUT':
                    print(f"\n[Step {step}] USER INPUT:\n{content}\n")
                elif t == 'PLANNER_RESPONSE' and len(content) > 0:
                    # Only print some summaries or key points to avoid huge text
                    lines = content.split('\n')
                    summary_lines = [l for l in lines if any(x in l.lower() for x in ['bug', 'station', 'absensi', 'nodemcu', 'esp32', 'rfid', 'response', 'parse', 'conclusion', 'perbaikan', 'fix', 'github', 'push'])]
                    if summary_lines:
                        print(f"[Step {step}] MODEL PLANNER (filtered):")
                        for sl in summary_lines[:15]:
                            print("  ", sl)
    except Exception as e:
        print(f"Error reading {conv_id}: {e}")

print_conv("91698023-f768-4a10-8fb2-44a93324d3ee")
print_conv("626cf84c-af02-46c1-8ad9-982eca1fe92e")

