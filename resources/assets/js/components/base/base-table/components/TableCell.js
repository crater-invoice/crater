export default {
  functional: true,

  props: ['column', 'row', 'responsiveLabel'],

  render (createElement, { props }) {
    const data = {}

    if (props.column.cellClass) {
      data.class = props.column.cellClass
    }

    if (props.column.template) {
      return createElement('td', data, props.column.template(props.row.data))
    }

    data.domProps = {}
    data.domProps.innerHTML = props.column.formatter(props.row.getValue(props.column.show), props.row.data)

    return createElement('td', [
      createElement('span', props.responsiveLabel), data.domProps.innerHTML
    ])
  }
}
