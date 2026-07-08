import re

with open('src/pags/allentries.tsx', 'r') as f:
    content = f.read()

# Fix normalizeEntry
normalize_old = """  const featuredTypeRaw = typeof record.featuredType === "string" ? record.featuredType : null;
  const featuredTypes: FeaturedType =
    featuredTypeRaw === "hero" || featuredTypeRaw === "headline" || featuredTypeRaw === "category_hero" || featuredTypeRaw === "breaking" || featuredTypeRaw === "las_5_de_x"
      ? featuredTypeRaw
      : "none";
  const isFeatured = typeof record.isFeatured === "boolean" ? record.isFeatured : featuredType !== "none";"""

normalize_new = """  const featuredTypesRaw = Array.isArray(record.featuredTypes) ? record.featuredTypes : [];
  const featuredTypes = featuredTypesRaw.filter((t: any): t is FeaturedType => 
    t === "hero" || t === "headline" || t === "category_hero" || t === "breaking" || t === "las_5_de_x"
  );
  const isFeatured = typeof record.isFeatured === "boolean" ? record.isFeatured : featuredTypes.length > 0;"""

content = content.replace(normalize_old, normalize_new)

with open('src/pags/allentries.tsx', 'w') as f:
    f.write(content)
