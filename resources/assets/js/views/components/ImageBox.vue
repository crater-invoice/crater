<template>
  <div class="imgbox">
    <vue-dropzone
      id="dropzone"
      ref="myVueDropzone"
      :include-styling="true"
      :options="dropzoneOptions"
      @vdropzone-sending="sendingEvent"
      @vdropzone-success="successEvent"
      @vdropzone-max-files-exceeded="maximum"
      @vdropzone-file-added="getCustomeFile"
      @vdropzone-removed-file="removeFile"
    />
  </div>
</template>
<script>
import vue2Dropzone from 'vue2-dropzone'
import 'vue2-dropzone/dist/vue2Dropzone.min.css'
export default {
  components: {
    vueDropzone: vue2Dropzone
  },
  props: {
    additionaldata: {
      type: Array,
      default () {
        return []
      }
    },
    url: {
      type: String,
      default () {
        return ''
      }
    },
    router: {
      type: Object,
      default: null
    },
    paramname: {
      type: String,
      default () {
        return ''
      }
    },
    acceptedfiles: {
      type: String,
      default () {
        return ''
      }
    },
    dictdefaultmessage: {
      type: String,
      default () {
        return ''
      }
    },
    autoprocessqueue: {
      type: Boolean,
      default: true
    },
    method: {
      type: String,
      default: 'POST'
    }
  },
  data () {
    return {
      dropzoneOptions: {
        autoProcessQueue: this.autoprocessqueue,
        url: this.url,
        thumbnailWidth: 110,
        maxFiles: 1,
        paramName: this.paramname,
        acceptedFiles: this.acceptedfiles,
        uploadMultiple: false,
        dictDefaultMessage: '<font-awesome-icon icon="trash"/> ' + this.dictdefaultmessage,
        dictInvalidFileType: 'This file type is not supported.',
        dictFileTooBig: 'File size too Big',
        addRemoveLinks: true,
        method: this.method,
        headers: { 'Authorization': `Bearer ${window.Ls.get('auth.token')}`, 'Company': `${window.Ls.get('selectedCompany')}` }
      }
    }
  },
  watch: {
    url (newURL) {
      this.$refs.myVueDropzone.options.url = newURL
    }
  },
  created () {
    window.hub.$on('sendFile', this.customeSend)
  },
  methods: {
    sendingEvent (file, xhr, formData) {
      var i
      for (i = 0; i < this.additionaldata.length; i++) {
        for (var key in this.additionaldata[i]) {
          formData.append(key, this.additionaldata[i][key])
        }
      }
    },
    successEvent (file, response) {
      // window.toastr['success'](response.success)
    },
    maximum (file) {
      this.$refs.myVueDropzone.removeFile(file)
    },
    getCustomeFile (file) {
      this.$emit('takefile', true)
    },
    removeFile (file, error, xhr) {
      this.$emit('takefile', false)
    },
    customeSend () {
      this.$refs.myVueDropzone.processQueue()
    }
  }
}
</script>
