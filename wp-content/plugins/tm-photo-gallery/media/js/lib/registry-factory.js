window.Registry = ( function () {
	"use strict";
	var modules = { };
	var mode = 'grid';

	/**
	 * Test module
	 *
	 * @param module
	 * @returns {boolean}
	 * @private
	 */
	function _testModule( module ) {
		if ( module.getInstance && typeof module.getInstance === 'function' ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Register module
	 *
	 * @param name
	 * @param module
	 */
	function register( name, module ) {
		if ( _testModule( module ) ) {
			modules[name] = module;
		} else {
			throw new Error( 'Invalide module "' + name + '". The function "getInstance" is not defined.' );
		}
	}

	/**
	 * Register modules
	 *
	 * @param map
	 */
	function registryMap( map ) {
		for ( var name in map ) {
			if ( !map.hasOwnProperty( name ) ) {
				continue;
			}
			if ( _testModule( map[name] ) ) {
				modules[name] = map[name];
			} else {
				throw new Error( 'Invalide module "' + name + '" inside the collection. The function "getInstance" is not defined.' );
			}
		}
	}

	/**
	 * Unregister
	 *
	 * @param name
	 */
	function unregister( name ) {
		delete modules[name];
	}

	/**
	 * Get instance module
	 *
	 * @param name
	 * @returns {*|wp.mce.View}
	 */
	function _get( name ) {
		var module = modules[name];
		if ( !module ) {
			throw new Error( 'The module "' + name + '" has not been registered or it was unregistered.' );
		}

		if ( typeof module.getInstance !== 'function' ) {
			throw new Error( 'The module "' + name + '" can not be instantiated. ' + 'The function "getInstance" is not defined.' );
		}

		return modules[name].getInstance();
	}

	/**
	 * Get current mode
	 *
	 * @returns {string}
	 */
	function getMode() {
		return mode;
	}

	/**
	 * Set mode
	 *
	 * @param modeName
	 * @returns {string}
	 */
	function setMode( modeName ) {
		mode = modeName;
		return mode;
	}

	return {
		register: register,
		unregister: unregister,
		_get: _get,
		registryMap: registryMap,
		getMode: getMode,
		setMode: setMode
	};

} )();
