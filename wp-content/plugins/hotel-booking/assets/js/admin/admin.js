(function($){

	MPHB.Plugin = can.Construct.extend({
		myThis: null
	}, {
		data: null,

		init: function(el, args){
			MPHB.Plugin.myThis = this;
			this.data = MPHB._data;
			delete MPHB._data;
			var ctrls = $('.mphb-ctrl:not([data-inited])');
			this.setControls(ctrls);
		},
		getVersion: function(){
			return this.data.version;
		},
		getPrefix: function(){
			return this.data.prefix;
		},
		addPrefix: function(str, separator){
			separator = (typeof separator !== 'undefined') ? separator : '-';
			return this.getPrefix() + separator + str;
		},
		setControls: function(ctrls){
			var ctrl, type;
			$.each(ctrls, function(){
				type = $(this).attr('data-type');
				switch( type ) {
					case 'text':
						break;
					case 'number':
						break;
					case 'total-price':
						ctrl = new MPHB.TotalPriceCtrl($(this));
						break;
					case 'gallery':
						ctrl = new MPHB.GalleryCtrl($(this));
						break;
					case 'datepicker':
						ctrl = new MPHB.DatePickerCtrl($(this));
						break;
					case 'color-picker':
						ctrl = new MPHB.ColorPickerCtrl($(this));
						break;
					case 'complex':
						ctrl = new MPHB.ComplexCtrl($(this));
						break;
					case 'complex-vertical':
						ctrl = new MPHB.ComplexVerticalCtrl($(this));
						break;
					case 'dynamic-select':
						ctrl = new MPHB.DynamicSelectCtrl($(this));
						break;
				}
				$(this).attr('data-inited', true);
			});
		}
	});


	MPHB.WPGallery = can.Construct.extend({
		myThis: null,
		getInstance: function(){
			if (MPHB.WPGallery.myThis === null) {
				MPHB.WPGallery.myThis = new MPHB.WPGallery();
			}
			return MPHB.WPGallery.myThis;
		}
	},
	{
		frame: null,
		ctrl: null,
		init: function() {
			var self = this;
			MPHB.WPGallery.myThis = this;
			Attachment = wp.media.model.Attachment;

			wp.media.controller.MPHBGallery = wp.media.controller.FeaturedImage.extend({
				defaults: parent._.defaults({
					id: 'mphb-media-library-gallery',
					title: MPHB.Plugin.myThis.data.translations.roomTypeGalleryTitle,
					toolbar: 'main-insert',
					filterable: 'uploaded',
					library: wp.media.query({type: 'image'}),
					multiple: 'add',
					editable: true,
					priority: 60,
					syncSelection: false
				}, wp.media.controller.Library.prototype.defaults),
				updateSelection: function() {
					var selection = this.get('selection'),
						ids = MPHB.WPGallery.myThis.ctrl.get(),
						attachments;
					if ('' !== ids && -1 !== ids) {
						attachments = parent._.map(ids.split(/,/), function(id) {
							return Attachment.get(id);
						});
					}
					selection.reset(attachments);
				}
			});

			wp.media.view.MediaFrame.MPHBGallery = wp.media.view.MediaFrame.Post.extend({
				// Define insert - MPHB state
				createStates: function() {
					var options = this.options;

					// Add the default states
					this.states.add([
						// Main states
						new wp.media.controller.MPHBGallery()
					]);
				},
				// Removing let menu from manager
				bindHandlers: function() {
					wp.media.view.MediaFrame.Select.prototype.bindHandlers.apply(this, arguments);
					this.on('toolbar:create:main-insert', this.createToolbar, this);

					var handlers = {
						content: {
							'embed': 'embedContent',
							'edit-selection': 'editSelectionContent'
						},
						toolbar: {
							'main-insert': 'mainInsertToolbar'
						}
					};

					parent._.each(handlers, function(regionHandlers, region) {
						parent._.each(regionHandlers, function(callback, handler) {
							this.on(region + ':render:' + handler, this[ callback ], this);
						}, this);
					}, this);
				},
				// Changing main button title
				mainInsertToolbar: function(view) {
					var controller = this;

					this.selectionStatusToolbar(view);

					view.set('insert', {
						style: 'primary',
						priority: 80,
						text: MPHB.Plugin.myThis.data.translations.addGalleryToRoomType,
						requires: {selection: true},
						click: function() {
							var state = controller.state(),
									selection = state.get('selection');

							controller.close();
							state.trigger('insert', selection).reset();
						}
					});
				}
			});

			this.frame = new wp.media.view.MediaFrame.MPHBGallery(parent._.defaults({}, {
				state: 'mphb-media-library-gallery',
				library: {type: 'image'},
				multiple: true
			}));

			this.frame.on('open', this.proxy('onOpen'));
			this.frame.on('insert', this.proxy('setImage'));
		},

		open: function(ctrl) {
			this.ctrl = ctrl;
			this.frame.open();
		},
		onOpen: function() {
			var frame = this.frame;
			frame.reset();
			var ids = this.ctrl.getArray();
			if ( ids.length ) {
				var attachment = null;
				ids.forEach(function(id) {
					attachment = wp.media.attachment(id);
					attachment.fetch();
					frame.state().get('selection').add(attachment);
				});
			}
		},
		setImage: function() {
			var ids = [];
			var models = this.frame.state().get('selection').models;
			$.each(models, function(key, model) {
				var attributes = model.attributes;
				ids.push(attributes.id);
			});
			this.ctrl.set(ids.join(','));
		}
	});

	MPHB.Ctrl = can.Control.extend({}, {
		parentForm: null,

		init: function(el, args){
			this.parentForm = this.element.closest('form');
		}
	});

	MPHB.GalleryCtrl = MPHB.Ctrl.extend({}, {
		input: null,
		previewHolder: null,
		addGalleryBtn: null,
		removeGalleryBtn: null,

		init: function(el, args){
			this._super(el, args);
			this.input = this.element.find('input[type=hidden]');
			this.previewHolder = this.element.find('img').on('click', this.proxy('organizeGallery'));
			this.addGalleryBtn = this.element.find('.mphb-admin-organize-gallery-add').on('click', this.proxy('organizeGallery'));
			this.removeGalleryBtn = this.element.find('.mphb-admin-organize-gallery-remove').on('click', this.proxy('removeGallery'));
		},

		get: function(){
			return this.input.val();
		},

		getArray: function(){
			var value = this.get();
			return value !== '' ? this.get().split(/,/) : [];
		},


		set: function(value){
			this.input.val(value);
			this.updatePreview();
			this.updateBtnsVisibility();
		},

		updateBtnsVisibility: function(){
			var value = this.get();
			if (value !== '') {
				this.removeGalleryBtn.removeClass('mphb-hide');
				this.addGalleryBtn.addClass('mphb-hide');
			} else {
				this.removeGalleryBtn.addClass('mphb-hide');
				this.addGalleryBtn.removeClass('mphb-hide');
			}
		},

		updatePreview: function(){
			var value = this.get();
			if (value !== '') {
				var previewId = value.split(',').shift();
				var attachment = wp.media.attachment(previewId);
				var previewSrc = attachment.attributes.sizes.medium.url;
				this.previewHolder.removeClass('mphb-hide').attr('src', previewSrc);
			} else {
				this.previewHolder.addClass('mphb-hide').attr('src', '');
			}
		},

		organizeGallery: function(e){
			e.preventDefault();
			MPHB.WPGallery.getInstance().open(this);
		},

		removeGallery: function(e){
			e.preventDefault();
			this.set('');
		}
	});

	MPHB.DatePickerCtrl = MPHB.Ctrl.extend({}, {

		input: null,

		dateFormat: 'mm/dd/yyyy',

		init: function(el, args){
			this._super(el, args);
			var self = this;
			this.input = this.element.find('input')
			var multiple = this.input.is('[data-multiple]');

			this.input.datepick({
				'dateFormat': self.dateFormat,
				'showSpeed': 0,
				'showOtherMonths': true,
				'multiSelect': (multiple) ? 999 : 0,
				'monthsToShow': 3,
				'multiSeparator': ','
			});
		}
	});

	MPHB.ColorPickerCtrl = MPHB.Ctrl.extend({}, {

		input: null,

		init: function(el, args){

			this._super(el, args);

			this.input = this.element.find('input')

			this.input.spectrum({
				allowEmpty: true,
				preferredFormat: "hex",
				showInput: true,
				showInitial: true,
				showAlpha: false
			});

		}
		
	});

	MPHB.ComplexCtrl = MPHB.Ctrl.extend({}, {
		prototypeItem: null,
		itemsHolder: null,
		lastIndex: null,
		uniqid: null,
		itemSelector: 'tr',
		metaName: null,

		init: function(el, args){
			this._super(el, args);
			this.uniqid = this.element.children('table').attr('data-uniqid');
			this.metaName = this.element.children('input[type="hidden"]:first-of-type').attr('name');
			this.initItemsHolder();
			this.initAddBtn();
			this.initDeleteBtns();
			this.preparePrototypeItem();
			this.initLastIndex();
			this.setKeys(this.itemsHolder.children(this.itemSelector));
		},

		initLastIndex: function(){
			this.lastIndex = this.itemsHolder.children(this.itemSelector).length - 1;
		},

		initItemsHolder: function(){
			this.itemsHolder = this.element.children('table').children('tbody');
		},

		initAddBtn: function(){
			var self = this;
			this.element.on('click', '.mphb-complex-add-item[data-id="' + this.uniqid + '"]',function(e){
				self.addItem();
			})
		},

		initDeleteBtns: function(){
			var self = this;
			this.itemsHolder.on( 'click','.mphb-complex-delete-item[data-id="' + this.uniqid + '"]', function(e){
				self.deleteItem($(this).closest(self.itemSelector));
			});
		},

		preparePrototypeItem: function(){
			var item = this.itemsHolder.children('.mphb-complex-item-prototype');
			this.prototypeItem = item.clone();
			this.prototypeItem.removeClass('mphb-hide mphb-complex-item-prototype').find('[name]').each(function(){
				$(this).removeAttr('disabled');
			});

			item.remove();
		},

		getIncIndex: function(){
			return ++this.lastIndex;
		},

		setKeys: function(wrappers){
			var self = this;
			var name, id, forAttr, key, $wrapper;
			var keyRegEx = new RegExp('%key_' + this.uniqid + '%', 'g');
			var keyPlaceholder = '%key_' + this.uniqid + '%';
			wrappers.each(function(index, wrapper){
				$wrapper = $(wrapper);
				key = $wrapper.attr('data-id');

				if (key === keyPlaceholder){
					key = self.getIncIndex();
					$wrapper.attr('data-id', key);
				}
				$wrapper.find('[name*="[%key_' + self.uniqid + '%]"]').each(function(){
					name = $(this).attr('name').replace(keyRegEx, key);
					$(this).attr('name', name)
					if ($(this).attr('id')) {
						id = $(this).attr('id').replace(keyRegEx, key).replace(/\[|\]/g, '__');
						$(this).attr('name', name).attr('id', id);
					}
				});
				$wrapper.find('[for*="[%key_' + self.uniqid + '%]"]').each(function(){
					forAttr = $(this).attr('for').replace(keyRegEx, key).replace(/\[|\]/g, '__');
					$(this).attr('for', forAttr);
				});
			});
		},

		clonePrototypeItem: function(){
			var clonedItem = this.prototypeItem.clone();
			this.setKeys(clonedItem);
			return clonedItem;
		},

		addItemToHolder: function(item){
			this.itemsHolder.append(item);
		},

		deleteItem: function(item){
			item.remove();
		},

		addItem: function(){
			var item = this.clonePrototypeItem();
			this.addItemToHolder(item);
			var ctrls = item.find('.mphb-ctrl:not([data-inited])');
			MPHB.Plugin.myThis.setControls(ctrls);
		}

	});

	MPHB.ComplexVerticalCtrl = MPHB.ComplexCtrl.extend({}, {

		itemSelector: 'tbody',
		lastIndexInput: null,
		minItemsCount: 0,

		init: function(el, args){
			this._super(el, args);
			this.minItemsCount = this.itemsHolder.attr('data-min-items-count');
		},

		initLastIndex: function(){
			this.lastIndexInput = this.itemsHolder.find('>tfoot .mphb-complex-last-index');
			this.lastIndex = this.lastIndexInput.val();
		},

		getIncIndex: function(){
			var index = this._super();
			this.lastIndexInput.val(index);
			return index;
		},

		initItemsHolder: function(){
			this.itemsHolder = this.element.children('table');
		},

		addItemToHolder: function(item){
			this.itemsHolder.children('tfoot').before(item);
		},

		disableDeleteButtons: function(){
			var deleteButtons = this.itemsHolder.children(this.itemSelector).children('.mphb-complex-item-actions-holder').find('.mphb-complex-delete-item');
			deleteButtons.attr('disabled', 'disabled').addClass('mphb-hide');
		},

		enableDeleteButtons: function(){
			var deleteButtons = this.itemsHolder.children(this.itemSelector).children('.mphb-complex-item-actions-holder').find('.mphb-complex-delete-item');
			deleteButtons.removeAttr('disabled').removeClass('mphb-hide');
		},

		updateItemActions: function(){
			var itemCount = this.itemsHolder.children(this.itemSelector).length;
			if (itemCount <= this.minItemsCount) {
				this.disableDeleteButtons();
			} else {
				this.enableDeleteButtons();
			}
		},

		updateDefaultItem: function(){
			var defaultRadio = this.itemsHolder.children(this.itemSelector).find('>.mphb-complex-item-actions-holder [name="' + this.metaName + '[default]"]');
			if ( !defaultRadio.filter(':checked').length ) {
				defaultRadio.first().attr('checked', 'checked');
			}
		},

		deleteItem: function(item){
			this._super(item);
			this.updateItemActions();
			this.updateDefaultItem();
		},

		addItem: function(){
			this._super();
			this.updateItemActions();
			this.updateDefaultItem();
		}

	});

	MPHB.TotalPriceCtrl = MPHB.Ctrl.extend({}, {

		preloader: null,
		input: null,

		init: function(el, args){
			this._super(el, args);
			this.input = this.element.find('input');
			this.recalculateBtn = this.element.find('#mphb-recalculate-total-price');
			this.errorsWrapper = this.element.find('.mphb-errors-wrapper');
			this.preloader = this.element.find('.mphb-preloader');
		},

		set: function( value ){
			this.input.val( value );
		},

		hideErrors: function(){
			this.errorsWrapper.empty().addClass('mphb-hide');
		},

		'input focus': function(){
			this.hideErrors();
		},

		showError: function( message ){
			this.errorsWrapper.html( message ).removeClass('mphb-hide');
		},

		'#mphb-recalculate-total-price click': function(el, e){
			var self = this;
			this.hideErrors();
			this.showPreloader();
			var data = this.parseFormToJSON();

			$.ajax({
				url: MPHB.Plugin.myThis.data.ajaxUrl,
				type: 'GET',
				dataType: 'json',
				data: {
					action: 'mphb_recalculate_total',
					mphb_nonce: MPHB.Plugin.myThis.data.nonces.mphb_recalculate_total,
					formValues: data
				},
				success: function(response) {
					if ( response.hasOwnProperty('success') ) {
						if ( response.success ) {
							self.set(response.data.total);
						} else {
							self.showError(response.data.message);
						}
					} else {
						self.showError(MPHB.Plugin.myThis.data.translations.errorHasOccured);
					}
				},
				error: function(jqXHR) {
					self.showError(MPHB.Plugin.myThis.data.translations.errorHasOccured);
				},
				complete: function(jqXHR){
					self.hidePreloader();
				}
			});
		},

		showPreloader: function(){
			this.recalculateBtn.attr('disabled', 'disabled');
			this.preloader.removeClass('mphb-hide');
		},

		hidePreloader: function(){
			this.recalculateBtn.removeAttr('disabled');
			this.preloader.addClass('mphb-hide');
		},

		parseFormToJSON: function(){
			return this.parentForm.serializeJSON();
		}

	});

	MPHB.DynamicSelectCtrl = MPHB.Ctrl.extend({}, {

		dependencyCtrl: null,
		ajaxAction: null,
		ajaxNonce: null,
		errorsWrapper: null,
		preloader: null,
		defaultOption: null,

		init: function(el, args){
			this._super(el, args);
			this.input = this.element.find('select');
			this.defaultOption = this.input.find('option[value=""]').clone();
			this.errorsWrapper = this.element.find('.mphb-errors-wrapper');
			this.preloader = this.element.find('.mphb-preloader');
			this.ajaxAction = this.input.attr('data-ajax-action');
			this.ajaxNonce = this.input.attr('data-ajax-nonce');

			this.initDependencyCtrl();
		},

		initDependencyCtrl: function(){
			var dependencyName = this.input.attr('data-dependency');
			this.dependencyCtrl = this.element.closest('form').find('[name="' + dependencyName + '"]');
			var self = this;
			this.dependencyCtrl.on('change', function(e){
				self.updateList();
			}).on('focus', function(e){
				self.hideErrors();
			});
		},

		setOptions: function(source){
			var self = this;
			this.input.html(this.defaultOption.clone());
			$.each(source, function(value, label){
				self.input.append($('<option />', {
					'value': value,
					'text': label
				}));
			});
		},

		updateList: function(){
			var self = this;
			this.hideErrors();
			this.showPreloader();
			this.input.html(this.defaultOption.clone());
			var data = this.parseFormToJSON();
			$.ajax({
				url: MPHB.Plugin.myThis.data.ajaxUrl,
				type: 'GET',
				dataType: 'json',
				data: {
					action: self.ajaxAction,
					mphb_nonce: self.ajaxNonce,
					formValues: data
				},
				success: function(response) {
					if ( response.hasOwnProperty('success') ) {
						if ( response.success ) {
							self.setOptions(response.data.options);
						} else {
							self.showError(response.data.message);
						}
					} else {
						self.showError(MPHB.Plugin.myThis.data.translations.errorHasOccured);
					}
				},
				error: function(jqXHR) {
					self.showError(MPHB.Plugin.myThis.data.translations.errorHasOccured);
				},
				complete: function(jqXHR){
					self.hidePreloader();
				}
			});
		},

		parseFormToJSON: function(){
			return this.parentForm.serializeJSON();
		},

		showPreloader: function(){
			this.preloader.removeClass('mphb-hide');
		},

		hidePreloader: function(){
			this.preloader.addClass('mphb-hide');
		},

		hideErrors: function(){
			this.errorsWrapper.empty().addClass('mphb-hide');
		},

		showError: function( message ){
			this.errorsWrapper.html( message ).removeClass('mphb-hide');
		}

	});

	new MPHB.Plugin();

	$(function(){
		if ($('.mphb-bookings-calendar-wrapper')) {
			new MPHB.BookingsCalendar($('.mphb-bookings-calendar-wrapper'));
		}
	});

	MPHB.BookingsCalendar = can.Control.extend({}, {
		filtersForm: null,
		customPeriodWrapper: null,
		btnPeriodPrev: null,
		btnPeriodNext: null,
		periodEl: null,
		init: function(el, args) {
			this.filtersForm = this.element.find('#mphb-bookings-calendar-filters');
			this.customPeriodWrapper = this.filtersForm.find('.mphb-custom-period-wrapper');
			this.btnPeriodPrev = this.filtersForm.find('.mphb-period-prev');
			this.btnPeriodNext = this.filtersForm.find('.mphb-period-next');
			this.periodEl = this.filtersForm.find('#mphb-bookings-calendar-filter-period');
			this.searchDateFromEl = this.filtersForm.find('.mphb-search-date-from');
			this.searchDateToEl = this.filtersForm.find('.mphb-search-date-to');
			this.initDatepickers();
		},
		initDatepickers: function(){
			var datepickers = this.filtersForm.find('.mphb-datepick');
			datepickers.datepick();
		},
		'#mphb-bookings-calendar-filter-period change': function(el, e) {
			var period = $(el).val();
			if (period === 'custom') {
				this.customPeriodWrapper.removeClass('mphb-hide');
				this.btnPeriodNext.addClass('mphb-hide');
				this.btnPeriodPrev.addClass('mphb-hide');
			} else {
				this.customPeriodWrapper.addClass('mphb-hide');
				this.btnPeriodNext.removeClass('mphb-hide');
				this.btnPeriodPrev.removeClass('mphb-hide');
			}
		},
		'#mphb-booking-calendar-search-room-availability-status change': function(el, e) {
			var status = $(el).val();
			if (status === '') {
				this.searchDateFromEl.addClass('mphb-hide');
				this.searchDateToEl.addClass('mphb-hide');
			} else {
				this.searchDateFromEl.removeClass('mphb-hide');
				this.searchDateToEl.removeClass('mphb-hide');
			}
		}

	});

})(jQuery);