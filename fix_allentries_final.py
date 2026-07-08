import re

with open('src/pags/allentries.tsx', 'r') as f:
    content = f.read()

# 1. replace updateFeaturedType
update_pattern = re.compile(r"const updateFeaturedType = async \(.*?\).*?catch \(err: unknown\) \{.*?\).*?\} finally \{.*?\}\n  \};", re.DOTALL)

update_new = """const toggleFeaturedType = async (
    entryId: string,
    currentTypes: FeaturedType[],
    toggledType: FeaturedType,
  ) => {
    let nextTypes = [...currentTypes];
    if (nextTypes.includes(toggledType)) {
      nextTypes = nextTypes.filter(t => t !== toggledType);
    } else {
      nextTypes.push(toggledType);
    }
    const nextIsFeatured = nextTypes.length > 0;

    setActionError(null);
    setTogglingFeaturedById((prev) => ({ ...prev, [entryId]: true }));
    setEntries((prev) =>
      prev.map((entry) =>
        entry.id === entryId
          ? { ...entry, featuredTypes: nextTypes, isFeatured: nextIsFeatured }
          : entry,
      ),
    );

    try {
      const payload = await apiFetch<{ isFeatured?: boolean; featuredTypes?: string[] }>(
        `${API_BASE_URL}/api/v1/article/${entryId}/feature`,
        {
          method: "PATCH",
          credentials: "include",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({
            isFeatured: nextIsFeatured,
            featuredTypes: nextTypes,
          }),
        },
      );

      const payloadTypes = Array.isArray(payload.featuredTypes) 
          ? payload.featuredTypes.filter((t: any): t is FeaturedType => 
            t === "hero" || t === "headline" || t === "category_hero" || t === "breaking" || t === "las_5_de_x"
          )
          : undefined;

      const resolvedTypes: FeaturedType[] = payloadTypes ?? (payload.isFeatured ? nextTypes : []);

      setEntries((prev) =>
        prev.map((entry) =>
          entry.id === entryId
            ? {
                ...entry,
                featuredTypes: resolvedTypes,
                isFeatured: typeof payload.isFeatured === "boolean" ? payload.isFeatured : resolvedTypes.length > 0,
              }
            : entry,
        ),
      );

      backgroundRefreshRef.current = true;
      setRefreshKey((prev) => prev + 1);
    } catch (err: unknown) {
      if (err instanceof ApiError && (err.status === 401 || err.status === 403)) {
        navigate("/adminlogin", { replace: true });
        return;
      }

      setEntries((prev) =>
        prev.map((entry) =>
          entry.id === entryId
            ? { ...entry, featuredTypes: currentTypes, isFeatured: currentTypes.length > 0 }
            : entry,
        ),
      );
      setActionError(
        err instanceof Error
          ? err.message
          : "No se pudo actualizar el tipo de destacado.",
      );
    } finally {
      setTogglingFeaturedById((prev) => {
        const next = { ...prev };
        delete next[entryId];
        return next;
      });
    }
  };"""

content = update_pattern.sub(update_new, content)

# 2. replace badges
badges_pattern = re.compile(r"\{entry\.featuredTypes !== \"none\" \? \(\s*<div className=\"entries-badges\">.*?</div>\s*\) : null\}", re.DOTALL)
badges_new = """{entry.featuredTypes && entry.featuredTypes.length > 0 ? (
                          <div className="entries-badges">
                            {entry.featuredTypes.includes("hero") && (
                              <span className="entries-badge badge-orange" title="Destacada Home">
                                Hero Home
                              </span>
                            )}
                            {entry.featuredTypes.includes("headline") && (
                              <span className="entries-badge badge-blue" title="Subdestacada Home">
                                Subdestacada
                              </span>
                            )}
                            {entry.featuredTypes.includes("las_5_de_x") && (
                              <span className="entries-badge badge-blue" title="Las 5 de X">
                                Las 5 de X
                              </span>
                            )}
                            {entry.featuredTypes.includes("category_hero") && (
                              <span className="entries-badge badge-orange" title="Destacada Categoría">
                                Hero Cat
                              </span>
                            )}
                            {entry.featuredTypes.includes("breaking") && (
                              <span className="entries-badge badge-red" title="Última Hora">
                                Última Hora
                              </span>
                            )}
                          </div>
                        ) : null}"""
content = badges_pattern.sub(badges_new, content)

# 3. replace dropdown code
dropdown_pattern = re.compile(r"<div\s*className=\{classNames\(\s*\"entries-featured-dropdown-trigger\",\s*`featured-\$\{entry\.featuredTypes\.join\('-\'\)\}`,.*?</div>\s*\)\}", re.DOTALL)
dropdown_new = """<div
                              className={classNames(
                                "entries-featured-dropdown-trigger",
                                entry.featuredTypes && entry.featuredTypes.length > 0 ? "active" : "",
                              )}
                              role="button"
                              tabIndex={0}
                              title={`Configurar Destacados`}
                              aria-haspopup="listbox"
                              aria-pressed={entry.featuredTypes && entry.featuredTypes.length > 0}
                              onClick={() => {
                                setOpenDropdownId(isOpen ? null : entry.id);
                              }}
                              onKeyDown={(e) => {
                                if (e.key === "Enter" || e.key === " ") {
                                  e.preventDefault();
                                  setOpenDropdownId(isOpen ? null : entry.id);
                                }
                              }}
                            >
                              <svg viewBox="0 0 24 24" fill={entry.featuredTypes && entry.featuredTypes.length > 0 ? "currentColor" : "none"} stroke="currentColor" strokeWidth="2">
                                <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                              </svg>
                            </div>
                            {isOpen && (
                              <div className="entries-featured-dropdown-menu" role="listbox">
                                {featuredOptions.filter(o => o.value !== 'none').map((option) => {
                                  const isSelected = entry.featuredTypes.includes(option.value as FeaturedType);
                                  return (
                                    <div
                                      key={option.value}
                                      role="option"
                                      aria-selected={isSelected}
                                      className={classNames(
                                        "entries-featured-option",
                                        isSelected ? "selected" : "",
                                      )}
                                      onClick={(e) => {
                                        e.preventDefault();
                                        e.stopPropagation();
                                        const type = option.value as FeaturedType;
                                        void toggleFeaturedType(entry.id, entry.featuredTypes, type);
                                      }}
                                    >
                                      <input type="checkbox" checked={isSelected} readOnly style={{marginRight: '8px', pointerEvents: 'none'}} />
                                      {option.label}
                                    </div>
                                  );
                                })}
                              </div>
                            )}"""
content = dropdown_pattern.sub(dropdown_new, content)

with open('src/pags/allentries.tsx', 'w') as f:
    f.write(content)

