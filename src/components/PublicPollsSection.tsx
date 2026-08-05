import { useCallback, useEffect, useState } from "react";
import { API_BASE_URL } from "../libs/config.ts";
import { apiFetch } from "../libs/http.ts";
import type { Poll } from "../libs/types.ts";
import "./PublicPollsSection.css";

export const PublicPollsSection = () => {
  const [polls, setPolls] = useState<Poll[]>([]);
  const [loading, setLoading] = useState<boolean>(true);
  
  // Voting states per poll ID
  const [selectedOptions, setSelectedOptions] = useState<Record<string, string[]>>({});
  const [isOtherSelected, setIsOtherSelected] = useState<Record<string, boolean>>({});
  const [otherText, setOtherText] = useState<Record<string, string>>({});
  const [submitting, setSubmitting] = useState<Record<string, boolean>>({});
  const [votedPolls, setVotedPolls] = useState<Record<string, boolean>>({});

  useEffect(() => {
    try {
      const stored = localStorage.getItem("voted_poll_ids");
      if (stored) {
        const ids: string[] = JSON.parse(stored);
        const map: Record<string, boolean> = {};
        ids.forEach(id => { map[id] = true; });
        setVotedPolls(map);
      }
    } catch (e) {
      console.warn("Error leyendo votos en localStorage", e);
    }
  }, []);

  const loadPublicPolls = useCallback(async () => {
    try {
      setLoading(true);
      const data = await apiFetch<Poll[]>(`${API_BASE_URL}/api/v1/polls/public`, {
        method: "GET"
      });
      setPolls(Array.isArray(data) ? data : []);
    } catch (err: unknown) {
      console.error("Error al cargar encuestas públicas:", err);
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    void loadPublicPolls();
  }, [loadPublicPolls]);

  const toggleOption = (poll: Poll, optionId: string) => {
    if (votedPolls[poll.id]) return;

    if (!poll.allowMultiple) {
      // Selección única
      setSelectedOptions(prev => ({ ...prev, [poll.id]: [optionId] }));
      setIsOtherSelected(prev => ({ ...prev, [poll.id]: false }));
    } else {
      // Selección múltiple
      const current = selectedOptions[poll.id] || [];
      const exists = current.includes(optionId);
      const updated = exists ? current.filter(id => id !== optionId) : [...current, optionId];
      setSelectedOptions(prev => ({ ...prev, [poll.id]: updated }));
    }
  };

  const toggleOther = (poll: Poll) => {
    if (votedPolls[poll.id]) return;

    if (!poll.allowMultiple) {
      // Selección única: activar "otro" y deseleccionar opciones
      setIsOtherSelected(prev => ({ ...prev, [poll.id]: true }));
      setSelectedOptions(prev => ({ ...prev, [poll.id]: [] }));
    } else {
      // Selección múltiple: alternar
      setIsOtherSelected(prev => ({ ...prev, [poll.id]: !prev[poll.id] }));
    }
  };

  const handleVote = async (poll: Poll) => {
    const opts = selectedOptions[poll.id] || [];
    const otherSel = isOtherSelected[poll.id];
    const txt = (otherText[poll.id] || "").trim();

    if (opts.length === 0 && (!otherSel || !txt)) {
      alert("Por favor selecciona una opción o escribe tu respuesta en 'Otro'.");
      return;
    }

    try {
      setSubmitting(prev => ({ ...prev, [poll.id]: true }));

      const res = await apiFetch<any>(`${API_BASE_URL}/api/v1/polls/${poll.id}/vote`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          optionIds: opts,
          otherText: otherSel ? txt : ""
        })
      });

      // Actualizar conteo en el estado local con los resultados regresados
      setPolls(prev => prev.map(p => {
        if (p.id !== poll.id) return p;
        return {
          ...p,
          options: res.options || p.options,
          otherVotes: typeof res.otherVotes === 'number' ? res.otherVotes : p.otherVotes
        };
      }));

      const nextVoted = { ...votedPolls, [poll.id]: true };
      setVotedPolls(nextVoted);
      try {
        localStorage.setItem("voted_poll_ids", JSON.stringify(Object.keys(nextVoted)));
      } catch (e) {
        console.warn(e);
      }
    } catch (err: unknown) {
      alert(err instanceof Error ? err.message : "Error al registrar el voto.");
    } finally {
      setSubmitting(prev => ({ ...prev, [poll.id]: false }));
    }
  };

  if (!loading && polls.length === 0) {
    return null;
  }

  return (
    <section className="public-polls-section">
      <div className="public-polls-container">
        <header className="public-polls-header">
          <span className="public-polls-subtitle">Voz de los Lectores</span>
          <h2 className="public-polls-title">Sondeos y Opinión Ciudadana</h2>
        </header>

        {loading ? (
          <div style={{ textAlign: "center", color: "#64748b", padding: "20px" }}>Cargando sondeos...</div>
        ) : (
          <div className="public-polls-grid">
            {polls.map((poll) => {
              const isVoted = Boolean(votedPolls[poll.id]);
              const selOpts = selectedOptions[poll.id] || [];
              const isOtherSel = Boolean(isOtherSelected[poll.id]);
              const txtValue = otherText[poll.id] || "";

              const totalVotes = (poll.options || []).reduce((acc, o) => acc + (o.votes || 0), 0) + (poll.otherVotes || 0);

              return (
                <div key={poll.id} className="public-poll-card">
                  {poll.imageUrl && (
                    <img src={poll.imageUrl} alt={poll.title} className="public-poll-header-image" />
                  )}
                  <div className="public-poll-body">
                    <div className="public-poll-meta">
                      <span className="public-poll-badge">
                        {poll.allowMultiple ? "Puedes elegir varias opciones" : "Elige una opción"}
                      </span>
                    </div>

                    <h3 className="public-poll-question">{poll.title}</h3>
                    {poll.description && (
                      <p className="public-poll-description">{poll.description}</p>
                    )}

                    {!isVoted ? (
                      /* Vista de Votación */
                      <>
                        <div className="public-poll-options-list" role={poll.allowMultiple ? "group" : "radiogroup"} aria-label={poll.title}>
                          {poll.options.map((opt) => {
                            const isSelected = selOpts.includes(opt.id);
                            return (
                              <div
                                key={opt.id}
                                className={`public-poll-option-item ${isSelected ? "selected" : ""}`}
                                onClick={() => toggleOption(poll, opt.id)}
                                role={poll.allowMultiple ? "checkbox" : "radio"}
                                aria-checked={isSelected}
                                tabIndex={0}
                                onKeyDown={(e) => {
                                  if (e.key === 'Enter' || e.key === ' ') {
                                    e.preventDefault();
                                    toggleOption(poll, opt.id);
                                  }
                                }}
                              >
                                <div className={`public-poll-option-radio-checkbox ${poll.allowMultiple ? "square" : "circle"}`}>
                                  {isSelected && <span className="public-poll-checkmark">{poll.allowMultiple ? "✓" : "●"}</span>}
                                </div>
                                <div className="public-poll-option-content">
                                  {opt.imageUrl && (
                                    <img src={opt.imageUrl} alt="" className="public-poll-option-image" />
                                  )}
                                  <span className="public-poll-option-text">{opt.text}</span>
                                </div>
                              </div>
                            );
                          })}

                          {poll.allowOther && (
                            <div className={`public-poll-other-box ${isOtherSel ? "selected" : ""}`}>
                              <div
                                className="public-poll-other-header"
                                onClick={() => toggleOther(poll)}
                              >
                                <div className={`public-poll-option-radio-checkbox ${poll.allowMultiple ? "square" : "circle"}`}>
                                  {isOtherSel && <span className="public-poll-checkmark" style={{ color: "white", background: "#a855f7", width: "100%", height: "100%", display: "flex", alignItems: "center", justifyContent: "center", borderRadius: poll.allowMultiple ? "6px" : "50%" }}>✓</span>}
                                </div>
                                <span className="public-poll-option-text" style={{ color: isOtherSel ? "#7e22ce" : "#1e293b" }}>
                                  Otra opción (escribe tu respuesta)...
                                </span>
                              </div>
                              {isOtherSel && (
                                <input
                                  type="text"
                                  className="public-poll-other-input"
                                  placeholder="Escribe aquí tu opinión..."
                                  value={txtValue}
                                  onChange={(e) => setOtherText(prev => ({ ...prev, [poll.id]: e.target.value }))}
                                  onClick={(e) => e.stopPropagation()}
                                  autoFocus
                                />
                              )}
                            </div>
                          )}
                        </div>

                        <button
                          type="button"
                          className="public-poll-submit-btn"
                          disabled={submitting[poll.id]}
                          onClick={() => handleVote(poll)}
                        >
                          {submitting[poll.id] ? "Enviando Voto..." : "Votar"}
                        </button>
                      </>
                    ) : (
                      /* Vista de Resultados en Tiempo Real */
                      <>
                        <div className="public-poll-results-box">
                          {poll.options.map((opt) => {
                            const percent = totalVotes > 0 ? Math.round(((opt.votes || 0) / totalVotes) * 100) : 0;
                            return (
                              <div key={opt.id} className="public-poll-result-card">
                                <div className="public-poll-result-header">
                                  <div style={{ display: "flex", alignItems: "center", gap: "8px" }}>
                                    {opt.imageUrl && <img src={opt.imageUrl} alt="" style={{ width: "24px", height: "24px", borderRadius: "4px", objectFit: "cover" }} />}
                                    <span>{opt.text}</span>
                                  </div>
                                  <span>{percent}% ({opt.votes || 0} votos)</span>
                                </div>
                                <div className="public-poll-result-bar-container">
                                  <div className="public-poll-result-bar" style={{ width: `${percent}%` }} />
                                </div>
                              </div>
                            );
                          })}
                          {poll.allowOther && (
                            <div className="public-poll-result-card">
                              <div className="public-poll-result-header">
                                <span>Otras respuestas de lectores</span>
                                <span>{totalVotes > 0 ? Math.round(((poll.otherVotes || 0) / totalVotes) * 100) : 0}% ({poll.otherVotes || 0} votos)</span>
                              </div>
                              <div className="public-poll-result-bar-container">
                                <div className="public-poll-result-bar other" style={{ width: `${totalVotes > 0 ? Math.round(((poll.otherVotes || 0) / totalVotes) * 100) : 0}%` }} />
                              </div>
                            </div>
                          )}
                        </div>

                        <div className="public-poll-thanks-badge">
                          <span>🎉 ¡Gracias! Tu opinión y voto han sido registrados.</span>
                        </div>
                      </>
                    )}
                  </div>
                </div>
              );
            })}
          </div>
        )}
      </div>
    </section>
  );
};
