import os
import re

for root, _, files in os.walk('src/pags'):
    for file in files:
        if file.endswith('.tsx'):
            path = os.path.join(root, file)
            with open(path, 'r') as f:
                content = f.read()

            # Fix allentries.tsx leftovers
            content = content.replace("entry.featuredType", "entry.featuredTypes")
            # For the dropdown aria-pressed fix in allentries.tsx (already fixed in my previous script? wait, there were leftovers)
            
            # Fix categorypage.tsx
            content = content.replace("a.featuredType === \"headline\"", "(a.featuredTypes || []).includes(\"headline\")")
            content = content.replace("a.featuredType === \"hero\"", "(a.featuredTypes || []).includes(\"hero\")")
            content = content.replace("a.featuredType === \"category_hero\"", "(a.featuredTypes || []).includes(\"category_hero\")")
            content = content.replace("a.featuredType == \"category_hero\"", "(a.featuredTypes || []).includes(\"category_hero\")")
            content = content.replace("a.featuredType !== \"hero\"", "!(a.featuredTypes || []).includes(\"hero\")")
            content = content.replace("a.featuredType !== \"headline\"", "!(a.featuredTypes || []).includes(\"headline\")")
            content = content.replace("a.featuredType !== \"category_hero\"", "!(a.featuredTypes || []).includes(\"category_hero\")")
            
            # fix mocked/dummy articles in categorypage.tsx normalizePublicArticle
            content = content.replace("featuredType:", "featuredTypes:")

            # Fix homepage.tsx
            content = content.replace("a.featuredType === 'las_5_de_x'", "(a.featuredTypes || []).includes('las_5_de_x')")
            content = content.replace("a.featuredType === 'hero'", "(a.featuredTypes || []).includes('hero')")
            content = content.replace("a.featuredType !== 'hero'", "!(a.featuredTypes || []).includes('hero')")
            content = content.replace("a.featuredType === 'headline'", "(a.featuredTypes || []).includes('headline')")
            content = content.replace("a.featuredType !== 'headline'", "!(a.featuredTypes || []).includes('headline')")

            with open(path, 'w') as f:
                f.write(content)
