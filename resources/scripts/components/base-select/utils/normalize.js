export default function normalize (str, strict = true) {
  return strict
    ? String(str).toLowerCase().trim()
    : String(str).normalize('NFD').replace(/\p{Diacritic}/gu, '').toLowerCase().trim()
}