import re

with open('src/pags/allentries.tsx', 'r') as f:
    content = f.read()

# Fix updateFeaturedType to toggleFeaturedType
content = content.replace("updateFeaturedType(", "toggleFeaturedType(")

# Fix entry.featuredTypes === 'string'
content = content.replace("entry.featuredTypes !== \"none\"", "entry.featuredTypes.length > 0")
content = content.replace("entry.featuredTypes === \"none\"", "entry.featuredTypes.length === 0")

content = content.replace("entry.featuredTypes === \"hero\"", "entry.featuredTypes.includes(\"hero\")")
content = content.replace("entry.featuredTypes === \"category_hero\"", "entry.featuredTypes.includes(\"category_hero\")")
content = content.replace("entry.featuredTypes === \"breaking\"", "entry.featuredTypes.includes(\"breaking\")")
content = content.replace("entry.featuredTypes === \"headline\"", "entry.featuredTypes.includes(\"headline\")")
content = content.replace("entry.featuredTypes === \"las_5_de_x\"", "entry.featuredTypes.includes(\"las_5_de_x\")")

# And any other comparisons I might have missed
content = content.replace("entry.featuredType !== \"none\"", "entry.featuredTypes.length > 0")
content = content.replace("entry.featuredType === \"none\"", "entry.featuredTypes.length === 0")

content = content.replace("entry.featuredType === \"hero\"", "entry.featuredTypes.includes(\"hero\")")
content = content.replace("entry.featuredType === \"category_hero\"", "entry.featuredTypes.includes(\"category_hero\")")
content = content.replace("entry.featuredType === \"breaking\"", "entry.featuredTypes.includes(\"breaking\")")
content = content.replace("entry.featuredType === \"headline\"", "entry.featuredTypes.includes(\"headline\")")
content = content.replace("entry.featuredType === \"las_5_de_x\"", "entry.featuredTypes.includes(\"las_5_de_x\")")


with open('src/pags/allentries.tsx', 'w') as f:
    f.write(content)
