<template>
  <div class="form-group image-radio">
    <div
      v-for="(pdfStyleList, index) in pdfStyleLists"
      :key="index"
      class="radio"
    >
      <label :for="pdfStyleList.val">
        <input
          v-model="checkedID"
          :value="pdfStyleList.val"
          :id="pdfStyleList.val"
          :checked="pdfStyleList.val == checkedID"
          type="radio"
          name="pdfSet"
          class="hidden"
        >
        <img
          :src="srcMaker(pdfStyleList.src)"
          alt="No Image"
          class="special-img"
        >
      </label>
    </div>
  </div>
</template>

<script>
export default{
  props: {
    currentPDF: {
      type: String,
      default: ''
    }
  },
  data () {
    return {
      pdfStyleLists: [
        {src: 'assets/img/PDF/Invoice1.png', val: '1'},
        {src: 'assets/img/PDF/Invoice2.png', val: '2'},
        {src: 'assets/img/PDF/Invoice3.png', val: '3'},
        {src: 'assets/img/PDF/Invoice4.png', val: '4'}
      ],
      checkedID: ''
    }
  },
  watch: {
    checkedID (newID) {
      if (newID !== null) {
        this.$emit('selectedPDF', newID)
      }
    }
  },
  mounted () {
    setTimeout(() => {
      if (this.currentPDF === '') {
        this.checkedID = null
      } else {
        this.checkedID = this.currentPDF
      }
    }, 1000)
  },
  methods: {
    srcMaker (file) {
      var url = '/'
      var full = url + '' + file
      return full
    }
  }
}
</script>
