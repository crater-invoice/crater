import Language from '../Language'

const language = new Language(
  'Korean',
  ['1월', '2월', '3월', '4월', '5월', '6월', '7월', '8월', '9월', '10월', '11월', '12월'],
  ['1월', '2월', '3월', '4월', '5월', '6월', '7월', '8월', '9월', '10월', '11월', '12월'],
  ['일', '월', '화', '수', '목', '금', '토']
)
language.yearSuffix = '년'
language.ymd = true

export default language
// eslint-disable-next-line
;
