(function($) {
	$(function() {

		//@todo builder concat
		MPHB.SearchForm = can.Control.extend({}, {

			checkInDatepickerEl: null,
			checkOutDatepickerEl: null,

			init: function(el, args){
				this.checkInDatepickerEl =  this.element.find('.mphb-datepick[name=mphb_check_in_date]');
				this.checkOutDatepickerEl = this.element.find('.mphb-datepick[name=mphb_check_out_date]');

				var self = this;

				this.checkInDatepickerEl.datepick({
					onSelect: function( dates ){
						var nextDay = dates.length ? $.datepick.add( dates[0], 1, 'd' ) : null;
						self.updateCheckOutMinDate( nextDay );
					},
					minDate: MPHB.RoomTypesDataManager.myThis.today,
					monthsToShow: MPHB._data.settings.numberOfMonthDatepicker,
					firstDay: MPHB._data.settings.firstDay
				});

				this.checkOutDatepickerEl.datepick({
					onSelect: function( dates ){},
					minDate: MPHB.RoomTypesDataManager.myThis.today,
					monthsToShow: MPHB._data.settings.numberOfMonthDatepicker,
					firstDay: MPHB._data.settings.firstDay
				});

				var checkInDate = this.checkInDatepickerEl.datepick('getDate');
				this.updateCheckOutMinDate( checkInDate.length ? $.datepick.add( checkInDate[0], 1, 'd' ) : null );
			},

			updateCheckOutMinDate: function( date ){
				if ( date !== null ) {
					this.setCheckOutMinDate( date );
				} else {
					this.unsetCheckOutMinDate();
				}
			},

			setCheckOutMinDate: function( minDate ){
				var checkOutDate = this.checkOutDatepickerEl.datepick('getDate');
				checkOutDate = checkOutDate.length ? checkOutDate[0] : null;

				this.checkOutDatepickerEl.datepick('option', 'minDate', minDate);

				if ( checkOutDate === null || checkOutDate <= minDate ) {
					this.checkOutDatepickerEl.datepick('setDate', minDate);
				}
			},

			unsetCheckOutMinDate: function(){
				this.checkOutDatepickerEl.datepick('option', 'minDate', MPHB._data.settings.today);
			}

		});

		MPHB.CheckoutForm = can.Control.extend({
			myThis: null
		}, {
			totalField: null,
			priceBreakdownWrapper: null,
			bookBtn: null,
			errorsWrapper: null,
			preloader: null,
			waitResponse: false,
			recalcTotalTimeout: null,

			init: function(el, args){
				MPHB.CheckoutForm.myThis = this;
				this.totalField = this.element.find('.mphb-total-price-field');
				this.bookBtn = this.element.find('input[type=submit]');
				this.errorsWrapper = this.element.find('.mphb-errors-wrapper');
				this.preloader = this.element.find('.mphb-preloader');
				this.priceBreakdownWrapper = this.element.find('.mphb-room-price-breakdown-wrapper');
			},

			setTotal: function( value ){
				this.totalField.html( value );
			},

			setupPriceBreakdown: function( priceBreakdown ){
				this.priceBreakdownWrapper.html( priceBreakdown );
			},

			setRate: function(rateId){
				this.recalculatePrices();
			},

			recalculatePrices: function(){
				var self = this;
				self.hideErrors();
				self.showPreloader();
				clearTimeout(this.recalcTotalTimeout);
				this.recalcTotalTimeout = setTimeout(function() {
					var data = self.parseFormToJSON();
					$.ajax({
						url: MPHB._data.ajaxUrl,
						type: 'GET',
						dataType: 'json',
						data: {
							action: 'mphb_recalculate_checkout_prices',
							mphb_nonce: MPHB._data.nonces.mphb_recalculate_checkout_prices,
							formValues: data
						},
						success: function(response) {
							if (response.hasOwnProperty('success')) {
								if (response.success) {
									self.setTotal(response.data.total);
									self.setupPriceBreakdown(response.data.priceBreakdown);
								} else {
									self.showError(response.data.message);
								}
							} else {
								self.showError(MPHB._data.translations.errorHasOccured);
							}
						},
						error: function(jqXHR) {
							self.showError(MPHB._data.translations.errorHasOccured);
						},
						complete: function(jqXHR) {
							self.hidePreloader();
						}
					});
				}, 500);
			},

			'[name="mphb_room_rate_id"] change': function(el, e){
				var rateId = el.val();
				this.setRate(rateId);
			},

			'.mphb_sc_checkout-services-list input, .mphb_sc_checkout-services-list select change': function(el, e){
				this.recalculatePrices();
			},

			hideErrors: function(){
				this.errorsWrapper.empty().addClass('mphb-hide');
			},

			showError: function( message ){
				this.errorsWrapper.html( message ).removeClass('mphb-hide');
			},

			showPreloader: function(){
				this.waitResponse = true;
				this.bookBtn.attr('disabled', 'disabled');
				this.preloader.removeClass('mphb-hide');
			},

			hidePreloader: function(){
				this.waitResponse = false;
				this.bookBtn.removeAttr('disabled');
				this.preloader.addClass('mphb-hide');
			},

			parseFormToJSON: function(){
				return this.element.serializeJSON();
			},

			'submit': function(el, e ){
				if (this.waitResponse) {
					return false;
				}
			}

		});

		MPHB.RoomTypeData = can.Construct.extend({}, {
			id: null,
			bookedDates: {},
			activeRoomsCount: 0,

			/**
			 *
			 * @param {Object}	data
			 * @param {Object}	data.bookedDates
			 * @param {int}		data.activeRoomsCount
			 * @returns {undefined}
			 */
			init: function(id, data){
				this.id = id;
				this.setRoomsCount( data.activeRoomsCount );
				this.setDates( data.dates );
			},

			update: function(data){
				if (data.hasOwnProperty('activeRoomsCount')) {
					this.setRoomsCount( data.activeRoomsCount );
				}

				if (data.hasOwnProperty('dates')) {
					this.setDates( data.dates );
				}

				$(window).trigger('mphb-update-room-type-data-' + this.id);
			},

			/**
			 *
			 * @param {int} count
			 * @returns {undefined}
			 */
			setRoomsCount: function(count){
				this.activeRoomsCount = count;
			},

			/**
			 *
			 * @param {Object} dates
			 * @param {Object} dates.bookedDates
			 * @returns {undefined}
			 */
			setDates: function( dates ){
				this.bookedDates = dates.hasOwnProperty('booked') ? dates.booked : {};
			},

			/**
			 * @param {String} dateFormatted Date in 'yyyy-mm-dd' format
			 */
			isAvailable: function( dateFormatted ){
				var lockedDates = this.getLockedDates();
				return !lockedDates.hasOwnProperty(dateFormatted) || lockedDates[dateFormatted] < this.activeRoomsCount;
			},

			/**
			 *
			 * @param {String} dateFormatted Date in 'yyyy-mm-dd' format
			 * @returns {Boolean}
			 */
			isBooked: function( dateFormatted ){
				var lockedDates = this.getLockedDates();
				return !lockedDates.hasOwnProperty(dateFormatted) || lockedDates[dateFormatted] < this.activeRoomsCount;
			},


			/**
			 *
			 * @param {Date} dateObj
			 * @returns {Date|false} Nearest locked room date if exists or false otherwise.
			 */
			getNearestLockedDate: function( dateObj ){
				var nearestDate = false;
				var self = this;

				$.each( self.getLockedDates(), function(bookedDate, count) {
					var bookedDateObj = new Date(bookedDate);
					if ( count >= self.activeRoomsCount && MPHB.Utils.formatDateToCompare( dateObj ) < MPHB.Utils.formatDateToCompare( bookedDateObj ) ) {
						nearestDate = bookedDateObj;
						return false;
					}
				});
				return nearestDate;
			},

			/**
			 *
			 * @returns {Object}
			 */
			getLockedDates: function(){
				var dates = {};
				return $.extend(dates, this.bookedDates);
			},

			/**
			 *
			 * @param {String} dateFormatted Date in 'yyyy-mm-dd' format
			 * @returns {String}
			 */
			getDateStatus: function( dateFormatted ){
				var status = MPHB.RoomTypesDataManager.ROOM_STATUS_AVAILABLE;
				if ( this.isEarlierThanToday( dateFormatted ) ) {
					status = MPHB.RoomTypesDataManager.ROOM_STATUS_PAST;
				} else if ( this.bookedDates.hasOwnProperty(dateFormatted) && this.bookedDates[dateFormatted] >= this.activeRoomsCount) {
					status = MPHB.RoomTypesDataManager.ROOM_STATUS_BOOKED;
				}
				return status;
			},

			/**
			 *
			 * @param {string} dateFormatted
			 * @returns {int}
			 */
			getAvailableRoomsCount: function( dateFormatted ){
				var count = this.bookedDates.hasOwnProperty(dateFormatted) ? this.activeRoomsCount - this.bookedDates[dateFormatted] : this.activeRoomsCount;
				if ( count < 0 ) {
					count = 0;
				}
				return count;
			},

			/**
			 *
			 * @param {String} formattedDate dateFormatted Date in 'yyyy-mm-dd' format
			 * @returns {Boolean}
			 */
			isEarlierThanToday: function( dateFormatted ){
				var dateObj = new Date(dateFormatted);
				return MPHB.Utils.formatDateToCompare(dateObj) < MPHB.Utils.formatDateToCompare(MPHB.RoomTypesDataManager.myThis.today);
			},

		});

		MPHB.RoomTypesDataManager = can.Construct.extend({
			myThis: null,
			ROOM_STATUS_AVAILABLE: 'available',
			ROOM_STATUS_BOOKED: 'booked',
			ROOM_STATUS_PAST: 'past'
		}, {
			today: null,
			roomTypesData: {},

			init: function(data) {
				MPHB.RoomTypesDataManager.myThis = this;
				this.initRoomTypesData(data.room_types_data);
				this.setToday(new Date(data.today))
			},

			/**
			 *
			 * @returns {undefined}
			 */
			initRoomTypesData: function( roomTypesData ) {
				var self = this;
				$.each( roomTypesData, function(id, data){
					self.roomTypesData[id] = new MPHB.RoomTypeData(id, data);
				});
			},

			setToday: function(date) {
				this.today = date;
			},

			/**
			 *
			 * @param {int|string} id ID of roomType
			 * @returns {MPHB.RoomTypeData|false}
			 */
			getRoomTypeData: function( id ) {
				return this.roomTypesData.hasOwnProperty(id) ? this.roomTypesData[id] : false;
			}

		});

		MPHB.RoomTypeCalendar = can.Control.extend({}, {

			roomTypeData: null,
			roomTypeId: null,

			init: function( el, args ){
				this.roomTypeId = parseInt(el.attr('id').replace(/^mphb-calendar-/, ''));
				this.roomTypeData = MPHB.RoomTypesDataManager.myThis.getRoomTypeData(this.roomTypeId);
				var self = this;
				el.hide().datepick({
					onDate: function(date, current){
						var dateClass = 'mphb-date-cell',
							dateTitle = '';

						if ( current ) {
							var dateFormatted = $.datepick.formatDate('yyyy-mm-dd', date);
							var status = self.roomTypeData.getDateStatus( dateFormatted );

							switch ( status ) {
								case MPHB.RoomTypesDataManager.ROOM_STATUS_PAST:
									dateClass += ' mphb-past-date';
									dateTitle = MPHB._data.translations.past;
									break;
								case MPHB.RoomTypesDataManager.ROOM_STATUS_AVAILABLE:
									dateClass += ' mphb-available-date';
									dateTitle = MPHB._data.translations.available + '(' + self.roomTypeData.getAvailableRoomsCount(dateFormatted) + ')';
									break;
								case MPHB.RoomTypesDataManager.ROOM_STATUS_BOOKED:
									dateClass += ' mphb-booked-date';
									dateTitle = MPHB._data.translations.booked;
									break;
							}
						} else {
							dateClass += ' mphb-extra-date';
						}

						return {
							selectable: false,
							dateClass: dateClass,
							title: dateTitle
						};
					},
					'minDate': MPHB.RoomTypesDataManager.myThis.today,
					'monthsToShow': MPHB._data.settings.numberOfMonthCalendar,
					'firstDay': MPHB._data.settings.firstDay
				}).show();

				$(window).on('mphb-update-room-type-data-' + this.roomTypeId, function(e){
					self.refresh();
				});

			},

			refresh: function(){
				this.element.hide();
				$.datepick._update(this.element[0], true);
				this.element.show();
			}

		});

		MPHB.ReservationForm = can.Control.extend({
			MODE_SUBMIT: 'submit',
			MODE_NORMAL: 'normal',
			MODE_WAITING: 'waiting'
		}, {
			/**
			 * @var jQuery
			 */
			formEl:					null,
			/**
			 * @var MPHB.RoomTypeCheckInDatepicker
			 */
			checkInDatepick:		null,
			/**
			 * @var MPHB.RoomTypeCheckOutDatepicker
			 */
			checkOutDatepick:		null,
			/**
			 * @var jQuery
			 */
			reserveBtn:				null,
			/**
			 * @var jQuery
			 */
			reserveBtnPreloader:	null,
			/**
			 * @var jQuery
			 */
			errorsWrapper:			null,
			/**
			 * @var String
			 */
			mode:					null,
			/**
			 * @var int
			 */
			roomTypeId:				null,
			/**
			 * @var MPHB.RoomTypeData
			 */
			roomTypeData:			null,

			setup: function(el, args){
				this._super(el, args);
				this.mode = MPHB.ReservationForm.MODE_NORMAL;
			},

			init: function(el, args){
				this.formEl = el;
				this.roomTypeId = parseInt(this.formEl.attr('id').replace(/^booking-form-/, ''));
				this.roomTypeData = MPHB.RoomTypesDataManager.myThis.getRoomTypeData(this.roomTypeId);
				this.errorsWrapper = this.formEl.find('.mphb-errors-wrapper');
				this.initCheckInDatepicker();
				this.initCheckOutDatepicker();
				this.initReserveBtn();

				this.updateDatepickers();
				var self = this;
				$(window).on('mphb-update-date-room-type-' + this.roomTypeId, function(){
					self.reservationForm.refreshDatepickers();
				});
			},

			updateDatepickers: function(){
				var checkInDate = this.checkInDatepick.getFormattedDate();
				if (checkInDate !== '') {
					if ( ! this.roomTypeData.isAvailable( checkInDate ) ) {
						this.checkInDatepick.clear();
					} else {
						this.onUpdateCheckInDate( this.checkInDatepick.getDate() );
					}
				}
			},

			'submit': function(el, e){

				if (this.mode !== MPHB.ReservationForm.MODE_SUBMIT) {
					e.preventDefault();
					e.stopPropagation();
					this.setFormWaitingMode();
					var self = this;
					$.ajax({
						url: MPHB._data.ajaxUrl,
						type: 'GET',
						dataType: 'json',
						data: {
							action: 'mphb_check_room_availability',
							mphb_nonce: MPHB._data.nonces.mphb_check_room_availability,
							roomTypeID: self.roomTypeId,
							checkInDate: this.checkInDatepick.getFormattedDate(),
							checkOutDate: this.checkOutDatepick.getFormattedDate()
						},
						success: function(response) {
							if ( response.hasOwnProperty('success') ) {
								if ( response.success ) {
									self.proceedToCheckout();
								} else {
									self.showError(response.data.message);
									self.roomTypeData.update(response.data.updatedData);
									self.clearDatepickers();
								}
							} else {
								self.showError(MPHB._data.translations.errorHasOccured);
							}
						},
						error: function(jqXHR) {
							self.showError(MPHB._data.translations.errorHasOccured);
						},
						complete: function(jqXHR){
							self.setFormNormalMode();
						}
					});
				}
			},

			proceedToCheckout: function(){
				this.mode = MPHB.ReservationForm.MODE_SUBMIT;
				this.unlock();
				this.formEl.submit();
			},

			showError: function(message){
				this.clearErrors();
				var errorMessage = $('<p>', {
					'class': 'mphb-error',
					'html': message
				});
				this.errorsWrapper.append(errorMessage).removeClass('mphb-hide');
			},

			clearErrors: function(){
				this.errorsWrapper.empty().addClass('mphb-hide');
			},

			lock: function(){
				this.element.find('[name]').attr('disabled', 'disabled');
				this.reserveBtn.attr('disabled', 'disabled').addClass('mphb-disabled');
				this.reserveBtnPreloader.removeClass('mphb-hide');
			},

			unlock: function(){
				this.element.find('[name]').removeAttr('disabled');
				this.reserveBtn.removeAttr('disabled', 'disabled').removeClass('mphb-disabled');
				this.reserveBtnPreloader.addClass('mphb-hide');
			},

			setFormWaitingMode: function(){
				this.mode = MPHB.ReservationForm.MODE_WAITING;
				this.lock();
			},

			setFormNormalMode: function(){
				this.mode = MPHB.ReservationForm.MODE_NORMAL;
				this.unlock();
			},

			initCheckInDatepicker: function() {
				var checkInEl = this.formEl.find('input[name=mphb_check_in_date]');
				this.checkInDatepick = new MPHB.RoomTypeCheckInDatepicker(checkInEl, {'form' : this});
			},

			initCheckOutDatepicker: function() {
				var checkOutEl = this.formEl.find('input[name=mphb_check_out_date]');
				this.checkOutDatepick = new MPHB.RoomTypeCheckOutDatepicker(checkOutEl, {'form' : this});
			},

			initReserveBtn: function(){
				this.reserveBtn = this.formEl.find('.mphb-reserve-btn');
				this.reserveBtnPreloader = this.formEl.find('.mphb-preloader');

				this.setFormNormalMode();
			},

			/**
			 *
			 * @param {Date|null} date
			 * @returns {undefined}
			 */
			onUpdateCheckInDate: function( date ) {
				if ( date !== null ) {
					this.setCheckOutDatepickLimitations(date);
				} else {
					this.clearCheckOutDatepickLimitations();
				}
			},

			/**
			 *
			 * @param {Date} checkInDate
			 * @returns {undefined}
			 */
			setCheckOutDatepickLimitations: function( checkInDate ){
				var checkInNextDay = $.datepick.add(new Date(checkInDate.getTime()), 1, 'd');
				this.checkOutDatepick.setOption('minDate', checkInNextDay);

				var maxDate = this.roomTypeData.getNearestLockedDate( checkInDate );
				if (maxDate) {
					this.checkOutDatepick.setOption('maxDate', maxDate);
				} else {
					this.checkOutDatepick.setOption('maxDate', '');
				}

				var checkOutDate = this.checkOutDatepick.getDate();
				if ( checkOutDate === null || checkOutDate < checkInNextDay || ( maxDate !== false && checkOutDate > maxDate ) ) {
					this.checkOutDatepick.setDate(checkInNextDay);
				}
			},

			clearCheckOutDatepickLimitations: function(){
				this.checkOutDatepick.setOption('minDate', MPHB.RoomTypesDataManager.myThis.today);
				this.checkOutDatepick.setOption('maxDate', '');
			},

			onUpdateCheckOutDate: function( date ){
				// Nothing
			},

			clearDatepickers: function(){
				this.checkInDatepick.clear();
				this.checkOutDatepick.clear();
			},

			refreshDatepickers: function(){
				this.checkInDatepick.refresh();
				this.checkOutDatepick.refresh();
			}

		});

		MPHB.RoomTypeDatepicker = can.Control.extend({}, {

			datepick:	null,
			form:		null,

			init: function(el, args){
				this.form = args.form;
			},

			/**
			 *
			 * @returns {Array}
			 */
			getRawDate: function(){
				// simulate getDate datepick method
				var dateStr = this.element.val();
				var dates = [];
				if (dateStr !== '') {
					$.each( dateStr.split(','), function(index, date){
						dates.push(new Date(date));
					});
				}

				return dates;
			},

			/**
			 * @return {Date|null}
			 */
			getDate: function(){
				var dateStr = this.element.val();
				return dateStr !== '' ? new Date(dateStr) : null;
			},

			/**
			 *
			 * @returns {String} Date in format 'yyyy-mm-dd' or empty string.
			 */
			getFormattedDate: function(){
				return this.element.val();
			},

			/**
			 * @param {Date} date
			 */
			setDate: function(date){
				this.element.datepick('setDate', date);
			},

			/**
			 * @param {string} option
			 */
			getOption: function(option){
				return this.element.datepick('option', option);
			},

			/**
			 * @param {string} option
			 * @param {mixed} value
			 */
			setOption: function(option, value){
				this.element.datepick('option', option, value);
			},

			/**
			 *
			 * @returns {Date|null}
			 */
			getMinDate: function(){
				var minDate = this.getOption('minDate');
				return minDate !== null && minDate !== '' ? new Date( minDate ) : null;
			},

			/**
			 *
			 * @returns {Date|null}
			 */
			getMaxDate: function(){
				var maxDate = this.getOption('maxDate');
				return maxDate !== null && maxDate !== '' ? new Date( maxDate ) : null;
			},

			clear: function(){
				this.element.datepick('clear');
			},

			/**
			 * @param {Date} date
			 * @param {string} format Optional. Default 'yyyy-mm-dd'.
			 */
			formatDate: function(date, format){
				format = typeof(format) !== 'undefined' ? format : 'yyyy-mm-dd';
				return $.datepick.formatDate( format, date );
			},

			/**
			 *
			 * @param {Date} date
			 * @returns {Boolean}
			 */
			isDateAvailable: function( date ){
				return self.form.roomTypeData.isAvailable( this.formatDate(date, 'yyyy-mm-dd') );
			},

			refresh: function(){
				$.datepick._update(this.element[0], true);
				$.datepick._updateInput(this.element[0], false);
			}

		});

		MPHB.RoomTypeCheckInDatepicker = MPHB.RoomTypeDatepicker.extend({}, {

			init: function(el, args){
				this._super(el, args);
				var self = this;
				this.element.datepick({
					onDate: function ( date, current ){
						var dateClass = 'mphb-date-cell',
							selectable = false,
							dateTitle = '';

						if ( current ) {
							var dateFormatted = $.datepick.formatDate('yyyy-mm-dd', date);
							var status = self.form.roomTypeData.getDateStatus( dateFormatted );

							switch ( status ) {
								case MPHB.RoomTypesDataManager.ROOM_STATUS_PAST:
									dateClass += ' mphb-past-date';
									dateTitle = MPHB._data.translations.past;
									break;
								case MPHB.RoomTypesDataManager.ROOM_STATUS_AVAILABLE:
									dateClass += ' mphb-available-date';
									dateTitle = MPHB._data.translations.available;
									selectable = true;
									break;
								case MPHB.RoomTypesDataManager.ROOM_STATUS_BOOKED:
									dateClass += ' mphb-booked-date';
									dateTitle = MPHB._data.translations.booked;
									break;
							}
						} else {
							dateClass += ' mphb-extra-date';
						}

						if ( selectable ) {
							dateClass += ' mphb-date-selectable';
						}

						return {
							selectable: selectable,
							dateClass: dateClass,
							title: dateTitle
						};
					},
					onSelect: function( dates ){
						self.form.onUpdateCheckInDate( dates.length ? dates[0] : null );
					},
					minDate: MPHB.RoomTypesDataManager.myThis.today,
					monthsToShow: MPHB._data.settings.numberOfMonthDatepicker,
					pickerClass: 'mphb-datepick-popup mphb-check-in-datepick',
					firstDay: MPHB._data.settings.firstDay
				});
			}

		});

		MPHB.RoomTypeCheckOutDatepicker = MPHB.RoomTypeDatepicker.extend({}, {

			init: function(el, args){
				this._super(el, args);
				var self = this;
				this.element.datepick({
					onDate: function ( date, current ){

						var dateClass = 'mphb-date-cell',
							selectable = false,
							dateTitle = '';

						if ( current ) {
							var prevDay = $.datepick.add( new Date(date), -1, 'd' );
							var prevDayFormatted = $.datepick.formatDate('yyyy-mm-dd', prevDay);
							var status = self.form.roomTypeData.getDateStatus( prevDayFormatted );

							var minDate = self.getMinDate();
							var maxDate = self.getMaxDate();
							var checkInDate = self.form.checkInDatepick.getDate();

							if ( checkInDate !== null && $.datepick.formatDate('yyyymmdd', date) === $.datepick.formatDate('yyyymmdd', checkInDate) ) {
								dateClass += ' mphb-check-in-date';
								dateTitle = MPHB._data.translations.checkInDate;
							} else if ( minDate !== null && $.datepick.formatDate('yyyymmdd', date) < $.datepick.formatDate('yyyymmdd', minDate) ) {
								dateClass += ' mphb-earlier-min-date';
								if ( status === MPHB.RoomTypesDataManager.ROOM_STATUS_PAST ) {
									dateClass += ' mphb-past-date';
								}
							} else if ( maxDate !== null && $.datepick.formatDate('yyyymmdd', date) > $.datepick.formatDate('yyyymmdd', maxDate) ) {
								dateClass += ' mphb-later-max-date';
							} else {
								switch ( status ) {
									case MPHB.RoomTypesDataManager.ROOM_STATUS_PAST:
										dateClass += ' mphb-past-date';
										dateTitle = MPHB._data.translations.past;
										break;
									case MPHB.RoomTypesDataManager.ROOM_STATUS_AVAILABLE:
										dateClass += ' mphb-available-date';
										dateTitle = MPHB._data.translations.available;
										selectable = true;
										break;
									case MPHB.RoomTypesDataManager.ROOM_STATUS_BOOKED:
										dateClass += ' mphb-booked-date';
										dateTitle = MPHB._data.translations.booked;
										break;
								}
							}
						} else {
							dateClass += ' mphb-extra-date';
						}

						if ( selectable ) {
							dateClass += ' mphb-selectable-date';
						} else {
							dateClass += ' mphb-unselectable-date';
						}

						return {
							selectable: selectable,
							dateClass: dateClass,
							title: dateTitle
						};
					},
					onSelect: function( dates ){
						self.form.onUpdateCheckOutDate( dates.length ? dates[0] : null );
					},
					minDate: MPHB.RoomTypesDataManager.myThis.today,
					monthsToShow: MPHB._data.settings.numberOfMonthDatepicker,
					pickerClass: 'mphb-datepick-popup mphb-check-out-datepick',
					firstDay: MPHB._data.settings.firstDay
				});
			}

		});

		MPHB.Utils = can.Construct.extend({

			/**
			 *
			 * @param {Date} date
			 * @returns {String}
			 */
			formatDateToCompare: function( date ){
				return $.datepick.formatDate('yyyymmdd', date);
			}
		}, {});

		//Initialization
		new MPHB.RoomTypesDataManager(MPHB._data);

		if ( MPHB._data.page.isCheckoutPage ) {
			new MPHB.CheckoutForm( $('.mphb_sc_checkout-form') );
		}

		var calendars = $('.mphb-calendar.mphb-datepick');
		$.each(calendars, function(index, calendarEl){
			new MPHB.RoomTypeCalendar( $(calendarEl) );
		});

		var reservationForms = $('.mphb-booking-form');
		$.each(reservationForms, function(index, formEl){
			new MPHB.ReservationForm( $(formEl) );
		});

		var searchForms = $('form.mphb_sc_search-form,form.mphb_widget_search-form');
		$.each( searchForms, function( index, formEl ) {
			new MPHB.SearchForm( $(formEl) );
		});

	});

})(jQuery);