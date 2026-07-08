import re

# Fix categorypage.tsx
with open('src/pags/categorypage.tsx', 'r') as f:
    content = f.read()

content = content.replace("a.featuredType === 'category_hero'", "(a.featuredTypes || []).includes('category_hero')")
content = content.replace("a.featuredType === 'none'", "(!a.featuredTypes || a.featuredTypes.length === 0)")
content = content.replace("a.featuredType === 'breaking'", "(a.featuredTypes || []).includes('breaking')")
content = content.replace("a.featuredType !== 'breaking'", "!(a.featuredTypes || []).includes('breaking')")
content = content.replace("r.featuredType ===", "(r.featuredTypes || []).includes")

# Fix r.featuredType in normalizePublicArticle mock inside categorypage.tsx
mock_old = """    featuredTypes:
      r.featuredType === "hero" ||
      r.featuredType === "headline" ||
      r.featuredType === "category_hero" ||
      r.featuredType === "breaking"
        ? (r.featuredType as any)
        : "none","""
mock_new = """    featuredTypes: Array.isArray(r.featuredTypes) ? r.featuredTypes : [],"""
content = content.replace(mock_old, mock_new)

with open('src/pags/categorypage.tsx', 'w') as f:
    f.write(content)


# Fix allentries.tsx
with open('src/pags/allentries.tsx', 'r') as f:
    content = f.read()

# Fix ArticleEntry interface if it still says featuredType
content = content.replace("featuredType: FeaturedType;", "featuredTypes: FeaturedType[];")
content = content.replace("entry.featuredType", "entry.featuredTypes")

# Any leftover TS errors
content = content.replace("featuredTypeLabel(entry.featuredTypes)", "entry.featuredTypes.map(featuredTypeLabel).join(', ')")
content = content.replace("`featured-${entry.featuredTypes}`", "`featured-${entry.featuredTypes.join('-')}`")

with open('src/pags/allentries.tsx', 'w') as f:
    f.write(content)

