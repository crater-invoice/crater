export default function arraysEqual (array1, array2) {
  const array2Sorted = array2.slice().sort()

  return array1.length === array2.length && array1.slice().sort().every(function(value, index) {
      return value === array2Sorted[index];
  })
}