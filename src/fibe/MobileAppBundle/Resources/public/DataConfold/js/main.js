require.config({
    baseUrl: 'js',
	shim: {
		backbone: {
			deps: [
				'underscore', 
				'jquery'
			],
			exports: 'Backbone'
		},
		jqueryMobile: {
            deps: [
                'jquery',
                'jqueryMobile.config'
            ],
        },
        'jqueryMobile.config': {
        	deps: [
                'jquery',
            ],
        },
		'underscore': {
			exports: '_'
		},
		'arbor':{
			deps : [
				'jquery'
			],
			exports: 'arbor'
		},
	},
    paths: {
        // the left side is the module ID,
        // the right side is the path to
        // the jQuery file, relative to baseUrl.
        // Also, the path should NOT include
        // the '.js' file extension. This example
        // is using jQuery 1.9.0 located at
        // js/lib/jquery-1.9.0.js, relative to
        // the HTML page.
        jquery: 'lib/jquery',
		backbone: 'lib/backbone',
		underscore : 'lib/underscore-min',
		'jqueryMobile.config' : 'lib/jquerymobile.config',
		'jsw' : 'reasoner/jsw',
		'jswui' : 'reasoner/jswui',
		'processOntoloy' : 'reasoner/processOntoloy',
		'query' : 'reasoner/query',
		'utilsMin' : 'reasoner/utils-min',
		'reasoner' : 'reasoner/Reasoner',
		'reasonerWorker' : 'reasoner/ReasonerWorker',
		'arbor': 'lib/arbor',
		'encoder': 'lib/encoder',
		'blob': 'lib/blob',
		'fileSaver' : 'lib/FileSaver',
		'jqueryMobile' : 'lib/jquery.mobile',
		'jStorage' : 'lib/jstorage.min',
		'moment' : 'lib/moment.min',
		'tpl' : 'lib/templateLoader',
		'ajaxLoader' : 'ajaxLoader/AjaxLoader',
		
    }

});
// Load the main app module to start the app
require(['app']);