<template>
  <div :class="[wrapperClass, isRtl ? 'rtl' : '']" class="base-date-input">
    <date-input
      :selected-date="selectedDate"
      :reset-typed-date="resetTypedDate"
      :format="customFormatter"
      :translation="translation"
      :inline="inline"
      :id="id"
      :name="name"
      :ref-name="refName"
      :open-date="openDate"
      :placeholder="placeholder"
      :input-class="inputClass"
      :typeable="typeable"
      :clear-button="clearButton"
      :clear-button-icon="clearButtonIcon"
      :calendar-button="calendarButton"
      :calendar-button-icon="calendarButtonIcon"
      :calendar-button-icon-content="calendarButtonIconContent"
      :disabled="disabled"
      :required="required"
      :class="{'required-date': invalid}"
      :bootstrap-styling="bootstrapStyling"
      :use-utc="useUtc"
      @showCalendar="showCalendar"
      @closeCalendar="close"
      @typedDate="setTypedDate"
      @clearDate="clearDate">
      <slot slot="afterDateInput" name="afterDateInput"/>
    </date-input>

    <!-- Day View -->
    <picker-day
      v-if="allowedToShowView('day')"
      :page-date="pageDate"
      :selected-date="selectedDate"
      :show-day-view="showDayView"
      :full-month-name="fullMonthName"
      :allowed-to-show-view="allowedToShowView"
      :disabled-dates="disabledDates"
      :highlighted="highlighted"
      :calendar-class="calendarClass"
      :calendar-style="calendarStyle"
      :translation="translation"
      :page-timestamp="pageTimestamp"
      :is-rtl="isRtl"
      :monday-first="mondayFirst"
      :day-cell-content="dayCellContent"
      :use-utc="useUtc"
      @changedMonth="handleChangedMonthFromDayPicker"
      @selectDate="selectDate"
      @showMonthCalendar="showMonthCalendar"
      @selectedDisabled="selectDisabledDate">
      <slot slot="beforeCalendarHeader" name="beforeCalendarHeader"/>
    </picker-day>

    <!-- Month View -->
    <picker-month
      v-if="allowedToShowView('month')"
      :page-date="pageDate"
      :selected-date="selectedDate"
      :show-month-view="showMonthView"
      :allowed-to-show-view="allowedToShowView"
      :disabled-dates="disabledDates"
      :calendar-class="calendarClass"
      :calendar-style="calendarStyle"
      :translation="translation"
      :is-rtl="isRtl"
      :use-utc="useUtc"
      @selectMonth="selectMonth"
      @showYearCalendar="showYearCalendar"
      @changedYear="setPageDate">
      <slot slot="beforeCalendarHeader" name="beforeCalendarHeader"/>
    </picker-month>

    <!-- Year View -->
    <picker-year
      v-if="allowedToShowView('year')"
      :page-date="pageDate"
      :selected-date="selectedDate"
      :show-year-view="showYearView"
      :allowed-to-show-view="allowedToShowView"
      :disabled-dates="disabledDates"
      :calendar-class="calendarClass"
      :calendar-style="calendarStyle"
      :translation="translation"
      :is-rtl="isRtl"
      :use-utc="useUtc"
      @selectYear="selectYear"
      @changedDecade="setPageDate">
      <slot slot="beforeCalendarHeader" name="beforeCalendarHeader"/>
    </picker-year>
  </div>
</template>
<script>
import en from './src/locale/translations/en'
import DateInput from './DateInput'
import PickerDay from './PickerDay.vue'
import PickerMonth from './PickerMonth.vue'
import PickerYear from './PickerYear.vue'
import utils, { makeDateUtils } from './src/DateUtils'
import { mapGetters } from 'vuex'
import moment from 'moment'

export default {
  components: {
    DateInput,
    PickerDay,
    PickerMonth,
    PickerYear
  },
  props: {
    value: {
      validator: val => utils.validateDateInput(val)
    },
    name: String,
    refName: String,
    id: String,
    // format: {
    //   type: [String, Function],
    //   default: 'dd MMM yyyy'
    // },
    language: {
      type: Object,
      default: () => en
    },
    openDate: {
      validator: val => utils.validateDateInput(val)
    },
    dayCellContent: Function,
    fullMonthName: Boolean,
    disabledDates: Object,
    highlighted: Object,
    placeholder: String,
    inline: Boolean,
    calendarClass: [String, Object, Array],
    inputClass: [String, Object, Array],
    wrapperClass: [String, Object, Array],
    mondayFirst: Boolean,
    clearButton: Boolean,
    clearButtonIcon: String,
    calendarButton: Boolean,
    calendarButtonIcon: String,
    calendarButtonIconContent: String,
    bootstrapStyling: Boolean,
    initialView: String,
    disabled: Boolean,
    required: Boolean,
    invalid: Boolean,
    typeable: Boolean,
    useUtc: Boolean,
    minimumView: {
      type: String,
      default: 'day'
    },
    maximumView: {
      type: String,
      default: 'year'
    }
  },
  data () {
    const startDate = this.openDate ? new Date(this.openDate) : new Date()
    const constructedDateUtils = makeDateUtils(this.useUtc)
    const pageTimestamp = constructedDateUtils.setDate(startDate, 1)
    return {
      /*
       * Vue cannot observe changes to a Date Object so date must be stored as a timestamp
       * This represents the first day of the current viewing month
       * {Number}
       */
      pageTimestamp,
      /*
       * Selected Date
       * {Date}
       */
      selectedDate: null,
      /*
       * Flags to show calendar views
       * {Boolean}
       */
      showDayView: false,
      showMonthView: false,
      showYearView: false,
      /*
       * Positioning
       */
      calendarHeight: 0,
      resetTypedDate: new Date(),
      utils: constructedDateUtils
    }
  },
  watch: {
    value (value) {
      this.setValue(value)
    },
    openDate () {
      this.setPageDate()
    },
    initialView () {
      this.setInitialView()
    }
  },
  computed: {
    ...mapGetters('preferences', {
      'format': 'getMomentDateFormat'
    }),
    customFormatter () {
      let newDate = new Date(this.value)
      return moment(newDate).format(this.format)
    },
    computedInitialView () {
      if (!this.initialView) {
        return this.minimumView
      }

      return this.initialView
    },
    pageDate () {
      return new Date(this.pageTimestamp)
    },

    translation () {
      return this.language
    },

    calendarStyle () {
      return {
        position: this.isInline ? 'static' : undefined
      }
    },
    isOpen () {
      return this.showDayView || this.showMonthView || this.showYearView
    },
    isInline () {
      return !!this.inline
    },
    isRtl () {
      return this.translation.rtl === true
    }
  },
  mounted () {
    this.init()
  },
  methods: {
    /**
     * Called in the event that the user navigates to date pages and
     * closes the picker without selecting a date.
     */
    resetDefaultPageDate () {
      if (this.selectedDate === null) {
        this.setPageDate()
        return
      }
      this.setPageDate(this.selectedDate)
    },
    /**
     * Effectively a toggle to show/hide the calendar
     * @return {mixed}
     */
    showCalendar () {
      if (this.disabled || this.isInline) {
        return false
      }
      if (this.isOpen) {
        return this.close(true)
      }
      this.setInitialView()
    },
    /**
     * Sets the initial picker page view: day, month or year
     */
    setInitialView () {
      const initialView = this.computedInitialView
      if (!this.allowedToShowView(initialView)) {
        throw new Error(`initialView '${this.initialView}' cannot be rendered based on minimum '${this.minimumView}' and maximum '${this.maximumView}'`)
      }
      switch (initialView) {
        case 'year':
          this.showYearCalendar()
          break
        case 'month':
          this.showMonthCalendar()
          break
        default:
          this.showDayCalendar()
          break
      }
    },
    /**
     * Are we allowed to show a specific picker view?
     * @param {String} view
     * @return {Boolean}
     */
    allowedToShowView (view) {
      const views = ['day', 'month', 'year']
      const minimumViewIndex = views.indexOf(this.minimumView)
      const maximumViewIndex = views.indexOf(this.maximumView)
      const viewIndex = views.indexOf(view)

      return viewIndex >= minimumViewIndex && viewIndex <= maximumViewIndex
    },
    /**
     * Show the day picker
     * @return {Boolean}
     */
    showDayCalendar () {
      if (!this.allowedToShowView('day')) {
        return false
      }
      this.close()
      this.showDayView = true
      return true
    },
    /**
     * Show the month picker
     * @return {Boolean}
     */
    showMonthCalendar () {
      if (!this.allowedToShowView('month')) {
        return false
      }
      this.close()
      this.showMonthView = true
      return true
    },
    /**
     * Show the year picker
     * @return {Boolean}
     */
    showYearCalendar () {
      if (!this.allowedToShowView('year')) {
        return false
      }
      this.close()
      this.showYearView = true
      return true
    },
    /**
     * Set the selected date
     * @param {Number} timestamp
     */
    setDate (timestamp) {
      const date = new Date(timestamp)
      this.selectedDate = date
      this.setPageDate(date)
      this.$emit('selected', date)
      this.$emit('input', date)
    },
    /**
     * Clear the selected date
     */
    clearDate () {
      this.selectedDate = null
      this.setPageDate()
      this.$emit('selected', null)
      this.$emit('input', null)
      this.$emit('cleared')
    },
    /**
     * @param {Object} date
     */
    selectDate (date) {
      this.setDate(date.timestamp)
      if (!this.isInline) {
        this.close(true)
      }
      this.resetTypedDate = new Date()
    },
    /**
     * @param {Object} date
     */
    selectDisabledDate (date) {
      this.$emit('selectedDisabled', date)
    },
    /**
     * @param {Object} month
     */
    selectMonth (month) {
      const date = new Date(month.timestamp)
      if (this.allowedToShowView('day')) {
        this.setPageDate(date)
        this.$emit('changedMonth', month)
        this.showDayCalendar()
      } else {
        this.selectDate(month)
      }
    },
    /**
     * @param {Object} year
     */
    selectYear (year) {
      const date = new Date(year.timestamp)
      if (this.allowedToShowView('month')) {
        this.setPageDate(date)
        this.$emit('changedYear', year)
        this.showMonthCalendar()
      } else {
        this.selectDate(year)
      }
    },
    /**
     * Set the datepicker value
     * @param {Date|String|Number|null} date
     */
    setValue (date) {
      if (typeof date === 'string' || typeof date === 'number') {
        let parsed = new Date(date)
        date = isNaN(parsed.valueOf()) ? null : parsed
      }
      if (!date) {
        this.setPageDate()
        this.selectedDate = null
        return
      }
      this.selectedDate = date
      this.setPageDate(date)
    },
    /**
     * Sets the date that the calendar should open on
     */
    setPageDate (date) {
      if (!date) {
        if (this.openDate) {
          date = new Date(this.openDate)
        } else {
          date = new Date()
        }
      }
      this.pageTimestamp = this.utils.setDate(new Date(date), 1)
    },
    /**
     * Handles a month change from the day picker
     */
    handleChangedMonthFromDayPicker (date) {
      this.setPageDate(date)
      this.$emit('changedMonth', date)
    },
    /**
     * Set the date from a typedDate event
     */
    setTypedDate (date) {
      this.setDate(date.getTime())
    },
    /**
     * Close all calendar layers
     * @param {Boolean} emitEvent - emit close event
     */
    close (emitEvent) {
      this.showDayView = this.showMonthView = this.showYearView = false
      if (!this.isInline) {
        if (emitEvent) {
          this.$emit('closed')
        }
        document.removeEventListener('click', this.clickOutside, false)
      }
    },
    /**
     * Initiate the component
     */
    init () {
      if (this.value) {
        this.setValue(this.value)
      }
      if (this.isInline) {
        this.setInitialView()
      }
    }
  },
}
// eslint-disable-next-line
;
</script>
<style lang="css">
.rtl {
	direction: rtl;
}

.required-date {
  border: 1px solid #FB7178;
  border-radius: 5px;
}

.vdp-datepicker {
	position: relative;
	text-align: left;
}

.vdp-datepicker * {
	box-sizing: border-box;
}

.vdp-datepicker__calendar {
	position: absolute;
	z-index: 100;
	background: #fff;
	width: 300px;
	border: 1px solid #ccc;
}

.vdp-datepicker__calendar header {
	display: block;
	line-height: 40px;
}

.vdp-datepicker__calendar header span {
	display: inline-block;
	text-align: center;
	width: 71.42857142857143%;
	float: left;
}

.vdp-datepicker__calendar header .prev,
.vdp-datepicker__calendar header .next {
	width: 14.285714285714286%;
	float: left;
	text-indent: -10000px;
	position: relative;
}

.vdp-datepicker__calendar header .prev:after,
.vdp-datepicker__calendar header .next:after {
	content: '';
	position: absolute;
	left: 50%;
	top: 50%;
	transform: translateX(-50%) translateY(-50%);
	border: 6px solid transparent;
}

.vdp-datepicker__calendar header .prev:after {
	border-right: 10px solid #000;
	margin-left: -5px;
}

.vdp-datepicker__calendar header .prev.disabled:after {
	border-right: 10px solid #ddd;
}

.vdp-datepicker__calendar header .next:after {
	border-left: 10px solid #000;
	margin-left: 5px;
}

.vdp-datepicker__calendar header .next.disabled:after {
	border-left: 10px solid #ddd;
}

.vdp-datepicker__calendar header .prev:not(.disabled),
.vdp-datepicker__calendar header .next:not(.disabled),
.vdp-datepicker__calendar header .up:not(.disabled) {
	cursor: pointer;
}

.vdp-datepicker__calendar header .prev:not(.disabled):hover,
.vdp-datepicker__calendar header .next:not(.disabled):hover,
.vdp-datepicker__calendar header .up:not(.disabled):hover {
	background: #eee;
}

.vdp-datepicker__calendar .disabled {
	color: #ddd;
	cursor: default;
}

.vdp-datepicker__calendar .flex-rtl {
	display: flex;
	width: inherit;
	flex-wrap: wrap;
}

.vdp-datepicker__calendar .cell {
	display: inline-block;
	padding: 0 5px;
	width: 14.285714285714286%;
	height: 40px;
	line-height: 40px;
	text-align: center;
	vertical-align: middle;
	border: 1px solid transparent;
}

.vdp-datepicker__calendar .cell:not(.blank):not(.disabled).day,
.vdp-datepicker__calendar .cell:not(.blank):not(.disabled).month,
.vdp-datepicker__calendar .cell:not(.blank):not(.disabled).year {
	cursor: pointer;
}

.vdp-datepicker__calendar .cell:not(.blank):not(.disabled).day:hover,
.vdp-datepicker__calendar .cell:not(.blank):not(.disabled).month:hover,
.vdp-datepicker__calendar .cell:not(.blank):not(.disabled).year:hover {
	border: 1px solid #4bd;
}

.vdp-datepicker__calendar .cell.selected {
	background: #4bd;
}

.vdp-datepicker__calendar .cell.selected:hover {
	background: #4bd;
}

.vdp-datepicker__calendar .cell.selected.highlighted {
	background: #4bd;
}

.vdp-datepicker__calendar .cell.highlighted {
	background: #cae5ed;
}

.vdp-datepicker__calendar .cell.highlighted.disabled {
	color: #a3a3a3;
}

.vdp-datepicker__calendar .cell.grey {
	color: #888;
}

.vdp-datepicker__calendar .cell.grey:hover {
	background: inherit;
}

.vdp-datepicker__calendar .cell.day-header {
	font-size: 75%;
	white-space: nowrap;
	cursor: inherit;
}

.vdp-datepicker__calendar .cell.day-header:hover {
	background: inherit;
}

.vdp-datepicker__calendar .month,
.vdp-datepicker__calendar .year {
	width: 33.333%;
}

.vdp-datepicker__clear-button,
.vdp-datepicker__calendar-button {
	cursor: pointer;
	font-style: normal;
}

.vdp-datepicker__clear-button.disabled,
.vdp-datepicker__calendar-button.disabled {
	color: #999;
	cursor: default;
}
</style>
