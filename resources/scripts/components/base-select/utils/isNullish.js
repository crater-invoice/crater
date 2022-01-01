export default function isNullish (val) {
  return [null, undefined, false].indexOf(val) !== -1
}