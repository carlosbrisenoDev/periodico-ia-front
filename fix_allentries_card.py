with open('src/pags/allentries.tsx', 'r') as f:
    content = f.read()

# Fix 1: featuredTypeLabel(entry.featuredTypes)
content = content.replace("featuredTypeLabel(entry.featuredTypes)", "entry.featuredTypes.map(featuredTypeLabel).join(', ')")

# Fix 2: const isSelected = entry.featuredTypes === option.value;
content = content.replace("const isSelected = entry.featuredTypes === option.value;", "const isSelected = entry.featuredTypes.includes(option.value as FeaturedType);")

# Fix 3: updateFeaturedType
content = content.replace("updateFeaturedType(entry.id, entry.featuredTypes, nextType)", "toggleFeaturedType(entry.id, entry.featuredTypes, nextType)")
# actually, in the original code, the signature was updateFeaturedType(entry.id, entry.featuredType, nextType) which I replaced entry.featuredType with entry.featuredTypes
content = content.replace("updateFeaturedType(entry.id, entry.featuredTypes, nextType)", "toggleFeaturedType(entry.id, entry.featuredTypes, nextType)")

with open('src/pags/allentries.tsx', 'w') as f:
    f.write(content)
