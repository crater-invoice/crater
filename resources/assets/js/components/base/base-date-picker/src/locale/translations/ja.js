import Language from '../Language'

const language = new Language(
  'Japanese',
  ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月'],
  ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月'],
  ['日', '月', '火', '水', '木', '金', '土']
)

language.yearSuffix = '年'
language.ymd = true

export default language
// eslint-disable-next-line
;
