import { ref, toRefs, computed, watch } from 'vue'

export default function useSearch (props, context, dep)
{
  const { preserveSearch } = toRefs(props)

  // ================ DATA ================

  const search = ref(props.initialSearch) || ref(null)

  const input = ref(null)


  // =============== METHODS ==============

  const clearSearch = () => {
    if (!preserveSearch.value) search.value = ''
  }

  const handleSearchInput = (e) => {
    search.value = e.target.value
  }

  const handlePaste = (e) => {
    context.emit('paste', e)
  }

  // ============== WATCHERS ==============

  watch(search, (val) => {
    context.emit('search-change', val)
  })

  return {
    search,
    input,
    clearSearch,
    handleSearchInput,
    handlePaste,
  }
}