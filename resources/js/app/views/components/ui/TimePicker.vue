<template>
    <v-dialog
      v-model="display"
      @keydown.esc="display = false"
      eager
      persistent
      :width="width"
      :disabled="disabled"
     >
        <template v-slot:activator="{ on }">
          <v-text-field
            v-on="on"
            :label="label"
            :value="formattedDatetime"
            :disabled="disabled"
            :loading="loading"
            :error-messages="errorMessages"
            :error-count="errorCount"
            :error="error"
            :hide-details="hideDetails"
            :append-icon="appendIcon"
            :prepend-icon="prependIcon"
            readonly
          >
          </v-text-field>
        </template>

        <v-card>
            <v-card-text style="padding: 0;">
                <v-time-picker
                    ref="timer"
                    class="v-time-picker-custom"
                    v-model="timePart"
                    scrollable
                    :format="timePickerFormat"
                    actions
                >
                </v-time-picker>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <slot name="actions"
                  :parent="this"
                >
                    <v-btn color="grey lighten-1" text @click.native="clearHandler">{{clearText}}</v-btn>
                    <v-btn color="green darken-1" text @click="okHandler">{{okText}}</v-btn>
                </slot>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
  import moment from 'moment'

  const DEFAULT_DATE_FORMAT = 'YYYY-MM-DD'
  const DEFAULT_TIME_FORMAT = 'HH:mm'
  const DEFAULT_TIME = '00:00'

  export default {
    name: 'time-picker',
    model: {
      prop: 'datetime',
      event: 'input'
    },
    props: {
      datetime: {
        type: [Date, String],
        default: null
      },
      label: {
        type: String,
        default: ''
      },
      width: {
        type: Number,
        default: 320
      },
      format: {
        type: String,
        default: 'HH:mm'
      },
      timePickerFormat: {
        type: String,
        default: '24hr'
      },
      locale: {
        type: String,
        default: 'en-us'
      },
      clearText: {
        type: String,
        default: 'CLEAR'
      },
      okText: {
        type: String,
        default: 'OK'
      },
      disabled: {
        type: Boolean,
        default: false
      },
      loading: {
        type: Boolean,
        default: false
      },
      errorMessages: {
        type: [String, Array],
        default: () => []
      },
      errorCount: {
        type: [Number, String],
        default: 1
      },
      error: {
        type: Boolean,
        default: false
      },
      hideDetails: {
        type: Boolean,
        default: false
      },
      appendIcon: {
        type: String
      },
      prependIcon: {
        type: String
      }
    },
    data () {
      return {
        display: false,
        dateSelected: false,
        timeSelected: false,
        activeTab: 0,
        selectedDatetime: null
      }
    },
    created () {
      if (this.datetime instanceof Date) {
        this.selectedDatetime = this.datetime
      } else if (typeof this.datetime === 'string' || this.datetime instanceof String) {
        // see https://stackoverflow.com/a/9436948
        // this.selectedDatetime = moment(this.datetime, this.format)
        this.selectedDatetime = this.datetime
      }
    },
    computed: {
      datePart: {
        get () {
          let val = this.selectedDatetime ? moment(this.selectedDatetime).format(DEFAULT_DATE_FORMAT) : ''
          return val
        },
        set (val) {
          this.dateSelected = true
          this.activeTab = 1

          let date = moment(val, DEFAULT_DATE_FORMAT)
          let hour = this.selectedDatetime ? moment(this.selectedDatetime).hour() : 0
          let minute = this.selectedDatetime ? moment(this.selectedDatetime).minute() : 0
          let input = moment().year(date.year()).month(date.month()).date(date.date()).hour(hour).minute(minute).second(0)
          this.selectedDatetime = input.toDate()
        }
      },
      timePart: {
        get () {
          let val = this.selectedDatetime ? this.selectedDatetime : DEFAULT_TIME
          
          return val
        },
        set (val) {
            
            this.timeSelected = true

            // let time = moment(val, DEFAULT_TIME_FORMAT)
            // let input = moment(this.selectedDatetime).hour(time.hour()).minute(time.minute()).second(0)
            this.selectedDatetime = val
            
        }
      },
      formattedDatetime () {
          
        // return this.datetime ? moment(this.datetime).format(this.format) : ''
        return this.datetime ? this.datetime : ''
      }
    },
    methods: {
      okHandler () {
        this.display = false
        this.activeTab = 0
        this.$refs.timer.selectingHour = true

          
        this.$emit('input', this.selectedDatetime)
      },
      clearHandler () {
        this.display = false
        this.activeTab = 0
        this.$refs.timer.selectingHour = true

        this.$emit('input', null)
      }
    }
  }
</script>
<style>
  .v-time-picker-custom {
    width: 100%;
  }
</style>
