export default {
  basicConfig: {
    name: '',
    from_name: '',
    from_address: '',
    cc: '',
    bcc: '',
    is_default: false,
    driver: 'smtp', // 'smtp', 'mail', 'sendmail', 'mailgun', 'ses'
    settings: '',
  },
  smtpConfig: {
    host: '',
    port: null,
    username: '',
    password: '',
    encryption: 'tls', // 'tls', 'ssl', 'starttls'
  },
  mailgunConfig: {
    domain: '',
    secret: '',
    endpoint: '',
  },
  sesConfig: {
    host: '',
    port: null,
    encryption: 'tls', // 'tls', 'ssl', 'starttls'
    ses_key: '',
    ses_secret: '',
  },
}
