import re

with open('src/pags/allentries.tsx', 'r') as f:
    content = f.read()

# 1. Update ArticleEntry interface
content = content.replace("featuredType: FeaturedType;", "featuredTypes: FeaturedType[];")

# 2. Update normalizeEntry
normalize_old = """  const featuredTypeRaw = typeof record.featuredType === "string" ? record.featuredType : null;
  const featuredType: FeaturedType =
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
content = content.replace("featuredType: isFeatured ? (featuredType === \"none\" ? \"hero\" : featuredType) : \"none\",", "featuredTypes: isFeatured && featuredTypes.length === 0 ? [\"hero\"] : featuredTypes,")

# 3. Update updateFeaturedType
update_old = """  const updateFeaturedType = async (
    entryId: string,
    currentType: FeaturedType,
    nextType: FeaturedType,
  ) => {
    const nextIsFeatured = nextType !== "none";

    setEntries((prev) =>
      prev.map((entry) =>
        entry.id === entryId
          ? { ...entry, featuredType: nextType, isFeatured: nextIsFeatured }
          : entry,
      ),
    );

    try {
      const payload = await apiFetch<{ isFeatured?: boolean; featuredType?: string }>(
        `${API_BASE_URL}/api/v1/article/${entryId}/feature`,
        {
          method: "PATCH",
          credentials: "include",
          body: JSON.stringify({
            isFeatured: nextIsFeatured,
            featuredType: nextType,
          }),
        },
      );

      const payloadType =
        payload.featuredType === "hero" ||
        payload.featuredType === "headline" ||
        payload.featuredType === "category_hero" ||
        payload.featuredType === "breaking" ||
        payload.featuredType === "las_5_de_x" ||
        payload.featuredType === "none"
          ? payload.featuredType
          : undefined;

      const resolvedType: FeaturedType = payloadType ?? (payload.isFeatured ? nextType : "none");

      setEntries((prev) =>
        prev.map((entry) =>
          entry.id === entryId
            ? {
                ...entry,
                featuredType: resolvedType,
                isFeatured: typeof payload.isFeatured === "boolean" ? payload.isFeatured : nextIsFeatured,
              }
            : entry,
        ),
      );
    } catch (err: unknown) {
      if (err instanceof ApiError && (err.status === 401 || err.status === 403)) {
        navigate("/adminlogin", { replace: true });
        return;
      }

      const previousIsFeatured = currentType !== "none";
      setEntries((prev) =>
        prev.map((entry) =>
          entry.id === entryId
            ? { ...entry, featuredType: currentType, isFeatured: previousIsFeatured }
            : entry,
        ),
      );
      setActionError("Error al actualizar estado destacado");
    }
  };"""

update_new = """  const toggleFeaturedType = async (
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
    
    // limit max types if needed, but for now allow any combinations
    const nextIsFeatured = nextTypes.length > 0;

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
                isFeatured: typeof payload.isFeatured === "boolean" ? payload.isFeatured : nextIsFeatured,
              }
            : entry,
        ),
      );
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
      setActionError("Error al actualizar estado destacado");
    }
  };"""

content = content.replace(update_old, update_new)

# 4. Update JSX rendering logic for featured badges
badges_old = """                        {entry.featuredType !== "none" ? (
                          <div className="entries-badges">
                            {(entry.featuredType === "hero" || entry.featuredType === "category_hero") && (
                              <span
                                className="entries-badge badge-orange"
                                title="Destacada principal (se muestra en grande arriba)"
                              >
                                {entry.featuredType === "hero" ? "Hero" : "Hero Cat"}
                              </span>
                            )}
                            {entry.featuredType === "breaking" && (
                              <span className="entries-badge badge-red" title="Última Hora">
                                Última Hora
                              </span>
                            )}
                            {(entry.featuredType === "hero" || entry.featuredType === "headline" || entry.featuredType === "las_5_de_x") && (
                              <span
                                className={`entries-badge ${entry.featuredType === "hero" ? "badge-orange" : "badge-blue"}`}
                                title={entry.featuredType === "hero" ? "Destacada en Primera Plana" : entry.featuredType === "las_5_de_x" ? "Las 5 de X" : "Subdestacada en Primera Plana"}
                              >
                                {entry.featuredType === "hero" ? "Destacada Home" : entry.featuredType === "las_5_de_x" ? "Las 5 de X" : "Subdestacada Home"}
                              </span>
                            )}
                          </div>
                        ) : null}"""

badges_new = """                        {entry.featuredTypes && entry.featuredTypes.length > 0 ? (
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

content = content.replace(badges_old, badges_new)


# 5. Update UI Dropdown to Checkboxes
dropdown_old = """                            <div
                              className={classNames(
                                "entries-featured-dropdown-trigger",
                                `featured-${entry.featuredType}`,
                                entry.featuredType !== "none" ? "active" : "",
                              )}
                              role="button"
                              tabIndex={0}
                              title={`Destacado: ${featuredTypeLabel(entry.featuredType)}`}
                              aria-haspopup="listbox"
                              aria-pressed={entry.featuredType !== "none"}
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
                              <svg viewBox="0 0 24 24" fill={entry.featuredType !== "none" ? "currentColor" : "none"} stroke="currentColor" strokeWidth="2">
                                <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                              </svg>
                            </div>
                            {isOpen && (
                              <div className="entries-featured-dropdown-menu" role="listbox">
                                {featuredOptions.map((option) => {
                                  const isSelected = entry.featuredType === option.value;
                                  return (
                                    <div
                                      key={option.value}
                                      role="option"
                                      aria-selected={isSelected}
                                      className={classNames(
                                        "entries-featured-option",
                                        isSelected ? "selected" : "",
                                      )}
                                      onClick={() => {
                                        setOpenDropdownId(null);
                                        const nextType = option.value as FeaturedType;
                                        if (nextType !== entry.featuredType) {
                                          void updateFeaturedType(entry.id, entry.featuredType, nextType);
                                        }
                                      }}
                                    >
                                      {option.label}
                                    </div>
                                  );
                                })}
                              </div>
                            )}"""

dropdown_new = """                            <div
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

content = content.replace(dropdown_old, dropdown_new)

with open('src/pags/allentries.tsx', 'w') as f:
    f.write(content)

