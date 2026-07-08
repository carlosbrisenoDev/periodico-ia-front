with open('src/pags/allentries.tsx', 'r') as f:
    content = f.read()

content = content.replace("featuredTypess", "featuredTypes")

with open('src/pags/allentries.tsx', 'w') as f:
    f.write(content)
