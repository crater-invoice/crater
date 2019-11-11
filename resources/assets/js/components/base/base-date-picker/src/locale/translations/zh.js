import Language from '../Language'

const language = new Language(
  'Chinese',
  ['一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月'],
  ['一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月'],
  ['日', '一', '二', '三', '四', '五', '六']
)
language.yearSuffix = '年'

export default language
// eslint-disable-next-line
;
