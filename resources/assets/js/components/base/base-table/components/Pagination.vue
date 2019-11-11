<template>
  <nav v-if="shouldShowPagination">
    <ul class="pagination justify-content-center">
      <li :class="{ disabled: pagination.currentPage === 1 }">
        <a
          :class="{ disabled: pagination.currentPage === 1 }"
          @click="pageClicked( pagination.currentPage - 1 )"
        >
          <i class="left chevron icon">«</i>
        </a>
      </li>
      <li v-if="hasFirst" :class="{ active: isActive(1) }" class="page-item">
        <a class="page-link" @click="pageClicked(1)">1</a>
      </li>
      <li v-if="hasFirstEllipsis"><span class="pagination-ellipsis">&hellip;</span></li>
      <li v-for="page in pages" :key="page" :class="{ active: isActive(page), disabled: page === '...' }" class="page-item">
        <a class="page-link" @click="pageClicked(page)">{{ page }}</a>
      </li>
      <li v-if="hasLastEllipsis"><span class="pagination-ellipsis">&hellip;</span></li>
      <li
        v-if="hasLast"
        :class="{ active: isActive(this.pagination.totalPages) }"
        class="page-item"
      >
        <a class="page-link" @click="pageClicked(pagination.totalPages)">
          {{ pagination.totalPages }}
        </a>
      </li>
      <li>
        <a
          :class="{ disabled: pagination.currentPage === pagination.totalPages }"
          @click="pageClicked( pagination.currentPage + 1 )"
        >
          <i class="right chevron icon">»</i>
        </a>
      </li>
    </ul>
  </nav>
</template>

<script>
export default {
  props: {
    pagination: {
      type: Object,
      default: () => ({})
    }
  },

  computed: {
    pages () {
      return this.pagination.totalPages === undefined
        ? []
        : this.pageLinks()
    },

    hasFirst () {
      return this.pagination.currentPage >= 4 || this.pagination.totalPages < 10
    },

    hasLast () {
      return this.pagination.currentPage <= this.pagination.totalPages - 3 || this.pagination.totalPages < 10
    },

    hasFirstEllipsis () {
      return this.pagination.currentPage >= 4 && this.pagination.totalPages >= 10
    },

    hasLastEllipsis () {
      return this.pagination.currentPage <= this.pagination.totalPages - 3 && this.pagination.totalPages >= 10
    },

    shouldShowPagination () {
      if (this.pagination.totalPages === undefined) {
        return false
      }

      if (this.pagination.count === 0) {
        return false
      }

      return this.pagination.totalPages > 1
    }
  },
  methods: {
    isActive (page) {
      const currentPage = this.pagination.currentPage || 1

      return currentPage === page
    },
    pageClicked (page) {
      if (page === '...' ||
            page === this.pagination.currentPage ||
            page > this.pagination.totalPages ||
            page < 1) {
        return
      }
      this.$emit('pageChange', page)
    },

    pageLinks () {
      const pages = []

      let left = 2
      let right = this.pagination.totalPages - 1

      if (this.pagination.totalPages >= 10) {
        left = Math.max(1, this.pagination.currentPage - 2)
        right = Math.min(this.pagination.currentPage + 2, this.pagination.totalPages)
      }
      for (let i = left; i <= right; i++) {
        pages.push(i)
      }

      return pages
    }
  }
}

</script>
