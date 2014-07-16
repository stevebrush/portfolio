/*! (c) Blackbaud, Inc. Flickr Gallery */
;(function (win) {


	var $ = win.jQuery;


	var _settings;
	var _defaults = {
		apiKey: null,
		collectionId: null,
		debug: false,
		display: "gallery", // or, preview
		endpoint: "//api.flickr.com/services/rest/",
		feed: "//api.flickr.com/services/feeds/photos_public.gne",
		localGalleryButtonUrl: null,
        localGalleryButtonLabel: "View all photos",
        flickrGalleryButtonLabel: "Flickr.com&nbsp;&rarr;",
		userId: null
	};


	var _blueprints = {
		preview: [
			'<div class="flickr-preview">',
				'<ul class="flickr-preview-list">',
					'{{#each previews}}',
						'<li class="flickr-preview-list-item">',
							'<a class="swipebox th thumbnail" rel="alumni_gallery_{{../info.relationship}}" href="{{this.src_lg}}" title="{{this.title}}">',
								'<img src="{{this.src_sm}}" alt="{{this.title}}">',
							'</a>',
						'</li>',
					'{{/each}}',
				'</ul>',
				'<p class="flickr-preview-controls">',
					'{{#if info.localLink}}',
						'<a class="button btn btn-primary flickr-preview-internal-link" href="{{info.localLink}}">{{{info.localLinkLabel}}}</a>',
					'{{/if}}',
					'<a class="button btn btn-default flickr-preview-external-link" href="{{info.link}}" target="_blank">{{{info.flickrGalleryButtonLabel}}}</a>',
				'</p>',
			'</div>'
		],
		gallery: [
			'<div class="flickr-home">',
				'{{#each galleries}}',
					'<div class="photoset row">',
						'<div class="small-3 columns col-sm-3">',
							'<div class="th thumbnail">',
								'<a href="{{this.url}}" class="flickr-load-photos" data-flickr-photoset-id="{{this.id}}">',
									'<img src="//farm{{this.photoset.farm}}.staticflickr.com/{{this.photoset.server}}/{{this.photoset.primary}}_{{this.photoset.secret}}_m.jpg">',
								'</a>',
							'</div>',
						'</div>',
						'<div class="small-9 columns col-sm-9">',
							'<h2 class="media-heading">',
								'<a href="{{this.url}}" class="flickr-load-photos" data-flickr-photoset-id="{{this.id}}">',
									'{{this.photoset.title._content}}',
								'</a>',
							'</h2>',
							'<div class="flickr-photoset-details">',
								'<div class="flickr-info-published"><strong>Published:</strong> {{this.created}}</div>',
								'<div class="flickr-info-totals"><strong>Total photos:</strong> {{this.photoset.photos}}</div>',
							'</div>',
						'</div>',
					'</div>',
				'{{/each}}',
			'</div>'
		],
		detail: [
			'<div class="flickr-detail">',
				'<div class="flickr-gallery-heading">',
					'<h2>',
						'{{title}}',
					'</h2>',
					'<ul class="flickr-gallery-menu">',
						'<li class="list-item-1">',
							'<a href="#" class="flickr-back-to-home">',
								'&larr;&nbsp;Back to gallery home',
							'</a>',
						'</li>',
						'<li class="list-item-2">',
							'Total images: {{total}}',
						'</li>',
						'<li class="list-item-3">',
							'<a href="//www.flickr.com/photos/{{owner}}/sets/{{id}}/" target="_blank">',
								'View on flickr.com&nbsp;&rarr;',
							'</a>',
						'</li>',
					'</ul>',
					'<div class="controls">',
						'<a class="swipebox-gallery button btn btn-default" href="#" class="flickr-button">',
							'View Gallery',
						'</a>',
					'</div>',
				'</div>',
				'<div class="row">',
					'{{#each photos}}',
						'<div class="small-12 large-3 columns col-sm-12 col-md-3">',
							'<a class="swipebox th thumbnail" rel="alumni_gallery_{{../relationship}}" href="{{this.src_lg}}" title="{{this.title}}">',
								'<img src="{{this.src_sm}}">',
							'</a>',
						'</div>',
					'{{/each}}',
				'</div>',
			'</div>'
		]
	};
	var _errorTimeout;
	var _id;
	var _index = 0;
	var _map = {
		galleries: [],
		photosets: [],
		previews: []
	};
	var _pane;
	var _responses;


	var build = {
		detailFor: function (id) {
			
			// Compile HTML
			var template = Handlebars.compile(_blueprints.detail.join(""));
			_pane.html(template(_map.photosets[id])).css({ "display": "block" });
			
			// Swipebox
			_pane.find('.swipebox').swipebox({
				useSVG: false
			});
			
			// Swipebox gallery button
			_pane.find('.swipebox-gallery').on('click', function (e) {
				e.preventDefault();
				$('.swipebox:eq(0)').trigger('click');
			});
			
			// Back to gallery home button
			_pane.find('.flickr-back-to-home').unbind('click').on('click', function (e) {
				e.preventDefault();
				build.gallery();
			});
			
		},
		gallery: function () {
			
			// Compile HTML
			var template = Handlebars.compile(_blueprints.gallery.join(""));
			_pane.html(template(_map)).css({ "display": "block" });
			
			// Photoset titles load photos on click
			_pane.find('.flickr-load-photos').on('click', function (e) {
			
				e.preventDefault();
				
				// Get the photoset ID from the data attribute
				var id = this.getAttribute("data-flickr-photoset-id");
				
				if (methods.photosetLoaded(id) === true) {
				
					// Don't load the photoset information again if it's already been loaded
					build.detailFor(id);
					
				} else {
				
					// Photoset hasn't been loaded in the past, load it now.
					$.getJSON($(this).attr('href'));
					
				}
			});
		},
		preview: function () {
			var template = Handlebars.compile(_blueprints.preview.join(""));
			_pane.html(template(_map));
			_pane.find('.swipebox').swipebox({
				useSVG: false
			});
		}
	};
	var flickr = {
		apiHandler: function (r) {
		
			flickr.clearTimeout();
			
			if (typeof console === "object" && _settings.debug === true) {
				console.log("Flickr API Response: ", r);
			}
			
			if (r.collections) {
				if (typeof r.collections === "object") {
				
					_responses.collection = r;
					BBI.storage.set(_id, _responses);
					
					flickr.fetchNextPhotoset();
					
				} else {
					_pane.html("<div class=\"alert alert-warning\">The Flickr Collection could not be found based on the ID provided.</div>").css({"display": "block"});
				}
				
			} else if (r.photoset && typeof r.photoset.photo === "undefined") {
				save.galleryItem(r);
				if (flickr.fetchNextPhotoset() === false) {
					build.gallery();
				}
				
			} else if (r.photoset && r.photoset.photo) {
				save.photosetItem(r);
				build.detailFor(r.photoset.id);
			}
		},
		clearTimeout: function () {
			win.clearTimeout(_errorTimeout);
		},
		feedHandler: function (r) {
			if (typeof console === "object" && _settings.debug === true) {
				console.log("Flickr Feed Response: ", r);
			}
			_responses.feed = r;
			BBI.storage.set(_id, _responses);
			flickr.clearTimeout();
			save.previewItems(r);
			build.preview();
		},
		fetchCollectionById: function (id) {
			var url;
			if (_responses.collection) {
				if (typeof console === "object" && _settings.debug === true) {
					console.log("Flickr Collection exists in memory; loading from BBI.storage...");
				}
				flickr.apiHandler(_responses.collection);
			} else {
				url = _settings.endpoint + '?method=flickr.collections.getTree&api_key=' + _settings.apiKey + '&collection_id=' + id + '&user_id=' + _settings.userId + '&format=json&callback=?';
				$.getJSON(url);
			}
		},
		fetchFeed: function () {
			var url;
			if (_responses.feed) {
				if (typeof console === "object" && _settings.debug === true) {
					console.log("Flickr Feed exists in memory; loading from BBI.storage...");
				}
				flickr.feedHandler(_responses.feed);
			} else {
				url = _settings.feed + '?id=' + _settings.userId + '&format=json&callback=?';
				$.getJSON(url);
			}
		},
		fetchNextPhotoset: function () {
			var photoset = methods.getCollectionResponseItem(_index);
			_index = _index + 1;
			if (typeof photoset === "object") {
				flickr.fetchPhotosetById(photoset.id);
				return;
			}
			return false;
		},
		fetchPhotosetById: function (id) {
			var url = _settings.endpoint + '?method=flickr.photosets.getInfo&api_key=' + _settings.apiKey + '&photoset_id=' + id + '&format=json&callback=?';
			$.getJSON(url);
		}
	};
	var methods = {
		getCollectionResponseItem: function (i) {
			return _responses.collection.collections.collection[0].set[i] || false;
		},
		photosetLoaded: function (id) {
			// Check if a photoset has been loaded in the past
			return (_map.photosets.hasOwnProperty(id));
		}
	};
	var save = {
		galleryItem: function (obj) {
			var photoset = obj.photoset;
			_map.galleries.push({
				photoset: photoset,
				id: photoset.id,
				url: _settings.endpoint + '?method=flickr.photosets.getPhotos&api_key=' + _settings.apiKey + '&photoset_id=' + photoset.id + '&extras=url_s,url_m,url_o%2Curl_o&format=json&callback=?',
				created: (function (d) {
					return (d.getMonth() + 1) + "/" + d.getDate() + "/" + d.getFullYear();
				}(new Date(photoset.date_update * 1000)))
			});
		},
		photosetItem: function (obj) {
			var temp = [];
			var p = obj.photoset;
			var photos = p.photo;
			
			_responses.photoset = obj;
			BBI.storage.set(_id, _responses);
			
			_map.photosets[p.id] = {
				id: p.id,
				title: p.title,
				total: p.total,
				owner: p.owner,
				relationship: p.title.replace(/ /g, "_")
			};
			for (var i = 0, len = photos.length; i < len; i++) {
				temp.push({
					src_lg: (typeof photos[i].url_o === "undefined") ? photos[i].url_m : photos[i].url_o,
					src_sm: photos[i].url_s,
					title: photos[i].title
				});
			}
			_map.photosets[p.id].photos = temp;
		},
		previewItems: function (r) {
			$.each(r.items, function () {
				_map.previews.push({
					title: this.title,
					src_lg: this.media.m.replace("_m.", "_b."),
					src_sm: this.media.m.replace("_m.", "_s.")
				});
			});
			_map.info = {
				relationship: r.title.replace(/ /g, "_"),
				link: r.link,
				localLinkLabel: _settings.localGalleryButtonLabel,
				flickrGalleryButtonLabel: _settings.flickrGalleryButtonLabel
			};
			if (_settings.localGalleryButtonUrl !== null) {
				_map.info.localLink = _settings.localGalleryButtonUrl
			}
		}
	};


	$.fn.FlickrGallery = function (options) {
		
		_settings = $.extend(true, {}, _defaults, options);
		_pane = $(this).addClass('flickr-gallery');
		_id = "flickr_gallery_" + _settings.collectionId;
		
		// JSONP doesn't return ajax errors, so we use timeouts instead:
		_errorTimeout = win.setTimeout(function () {
	        _pane.html("<p>The Flickr feed could not be loaded at this time.</p>");
		}, 8000);
		
		_responses = BBI.storage.get(_id) || {};
		
		switch (_settings.display) {
			case "gallery":
				flickr.fetchCollectionById(_settings.collectionId);
			break;
			case "preview":
				flickr.fetchFeed();
			break;
		}
		
		return this;
	};
	
	
	// Flickr's JSONP methods
	win.jsonFlickrApi = flickr.apiHandler;
	win.jsonFlickrFeed = flickr.feedHandler;


}(window));