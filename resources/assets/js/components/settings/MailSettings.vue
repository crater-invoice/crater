<script>
let mailgunComponent = {
  template: '#mailgun-template'
}

let sendgridComponent = {
  template: '#sendgrid-template'
}

let sparkPostComponent = {
  template: '#sparkpost-template'
}

let smtpComponent = {
  template: '#smtp-template'
}

export default {
  components: {
    mailgun: mailgunComponent,
    sendgrid: sendgridComponent,
    sparkpost: sparkPostComponent,
    smtp: smtpComponent
  },
  props: {
    view: {
      type: Array,
      require: true,
      default: Array
    }
  },
  data () {
    return {
      currentView: 'mailgun'
    }
  },
  mounted () {
    let views = ['mailgun', 'sendgrid', 'sparkpost', 'smtp']

    if (this.view && views.indexOf(this.view) > -1) {
      this.currentView = this.view
    };
  }
}
</script>
