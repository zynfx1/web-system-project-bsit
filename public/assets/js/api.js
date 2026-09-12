const postJSON = async (url, payload) => {
  const res = await fetch(url, {
    method: 'POST',
    credentials: 'include',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(payload),
  });

  const data = await res.json().catch(() => ({}));
  return { ok: res.ok, status: res.status, data };
};

const getJSON = async (url) => {
  const res = await fetch(url, {
    method: 'GET',
    credentials: 'include',
  });

  const data = await res.json().catch(() => ({}));
  return { ok: res.ok, status: res.status, data };
};