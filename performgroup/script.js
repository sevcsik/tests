/* Copyright (c) 2012 by Andras Sevcsik
 * Released as Public Domain */

var API_URL = 'http://xml.eplayer.performgroup.com/eplayer/mrss/104284id1osg61o2dtlrqdcfjp/9776e735a37743b9b7b7d7ff11b21603/';

var VideoModel = Backbone.Model.extend({});

var VideoCollection = Backbone.Collection.extend(
{
  model: VideoModel,
  lastPage: 0,
  sync: function(method, collection, userOptions)
  {
    if (method !== 'read')
    {
      userOptions.error(new Error('Only reading is implemented.'));
      return;
    }

    // Convert page number to 7-step intervals
    var page = 0;
    if (userOptions.data && userOptions.data.page != undefined)
      page = userOptions.data.page

    var from = page * 7 + 1;
    var to = page * 7 + 7;
    var url = API_URL + from + '-' + to;

    // Set up options, and merge with userOptions
    var options = {};
    options.crossDomain = true;
    options.dataType = 'jsonp';
    options.jsonp = '_clbk';
    options.data = { '_fmt' : 'jsonp' };
    $.extend(true, options, userOptions);
    delete options.data.page;

    var collection = this; // to avoid loss of context

    // Swap callbacks with internal ones
    var onSuccess = options.success ? options.success : function() {};
    options.success = function(data, textStatus, jqXHR)
    {
      // Update description
      collection.description = data.channel.description;
      collection.total = data.channel.page.total;

      var items = [];
      if (data.channel.item)
      {
        $.each(data.channel.item, function(i, e)
        {
          e.id = from + i; // Local id corresponding to API range
          items.push(e);
        });
        collection.lastPage = page;
      }

      // Call the original callback
      onSuccess(items);
    };
    var onError = options.error ? options.error : function() {};
    options.error = function(jqXHR, textStatus, errorThrown)
    {
      // Call the original callback
      onError(new Error('Sync failed!'));
    };

    // Initiate ajax request
    $.ajax(url, options);

    // incremept page
    this.lastPage++;
  },
  fetchNext: function(callback)
  {
    this.fetch(
    {
      data: { page: this.lastPage + 1 },
      success: callback,
      error: callback,
      add: true
    });
  },
  initialize: function(view)
  {
    _.bindAll(this);
    this.view = view;
  }
}
);

var VideoView = Backbone.View.extend(
{
  initialize: function(model)
  {
    _.bindAll(this);

    // We set local vars for easier access in callbacks
    var model = this.model = model;
    var $el = this.$el = $('[data-template="VideoView"]').clone()
      .removeAttr('data-template');

    // Update elements
    this.render = function()
    {
      $el.find('.title').html(model.get('title'));
      $el.find('.description').html(model.get('description'));

      var thumb = model.get('thumbnail');
      $el.find('.thumb').attr('height', thumb.height);
      $el.find('.thumb').attr('width', thumb.width);
      $el.find('.thumb').attr('src', thumb.url);

      // Just select the last bitrate.
      var video = model.get('group').content;
      video = video[video.length - 1];
      $el.find('.play').attr('href', video.url);

      // Calculate duration
      var seconds = parseInt(video.duration, 10);
      var minutes = parseInt(seconds / 60);
      seconds = seconds % 60;
      $el.find('.duration').html(minutes + ':' + seconds);
    };
    this.render();
    model.on('change', this.render); // re-render on changes
  },
});

var BrowserView = Backbone.View.extend(
{
  initialize: function(collection, parentElement, itemCount)
  {
    _.bindAll(this);

    // We set local vars for easier access in callbacks
    var collection = this.collection = collection;
    var $el = this.$el = $('[data-template="BrowserView"]').clone()
      .removeAttr('data-template');
    var listElement = $el.find('.video-list');
    var views = this.views = [];

    // Insert to parent
    if (parentElement) $(parentElement).append($el);

    // Keep up with new elements
    collection.on('add', function(model)
    {
      $el.find('.channel-description').html(collection.description);
      $el.find('.total').html(collection.total);

      var view = new VideoView(model);
      views.push(view);
      listElement.append(view.$el);
    });

    $el.addClass('loading');
    collection.fetch(
    {
      add: true,
      success: _.bind(function()
      {
        this.toPage(0);
        $el.removeClass('loading');
      }, this)
    });

    this.itemCount = itemCount ? itemCount : 5;

    // Assign callbacks to buttons
    this.$el.find('.forward-button').click(_.bind(function()
    {
      this.toPage(this.currentPage + 1);
    }, this));
    this.$el.find('.back-button').click(_.bind(function()
    {
      this.toPage(this.currentPage - 1);
    }, this));
  },

  // Navigate to a page: 'second' argument is to avoid infinite loops
  toPage: function(page, second)
  {
    // check & reset flags
    if ((page < 0) || (this.lastPage && page > this.lastPage))
      return;

    this.atEnd = false;
    this.atStart = false;

    var count = this.itemCount;

    var min = page * count;
    var max = page * count + count - 1;

    // Hide all elements not between min and max
    var visibleItems = 0;

    this.$el.find('.from').html(min + 1);
    this.$el.find('.to').html(max + 1);

    $.each(this.views, function(i, e)
    {
      if (i >= min && i <= max)
      {
        e.$el.show();
        visibleItems++;
      }
      else
      {
        e.$el.hide();
      }
    });

    this.currentPage = page;

    // If there aren't enough items, we need to fetch more, and try again
    if (visibleItems != count && !second)
    {
      this.$el.addClass('loading');
      this.collection.fetchNext( _.bind(function()
      {
        this.toPage(page, true);
        this.$el.removeClass('loading');
      }, this));
    }
    else if (visibleItems == 0 && second)
    {
      // Previous page was the last page.
      this.lastPage = page - 1;
      this.toPage(page - 1);
    }
    else if (visibleItems != count && second)
    {
      // We reached the last page, stay here.
      this.lastPage = page;
    }
  }
});

// Initialize
$(function()
{
  // Set up the VideoCollection object
  var vc = new VideoCollection(/* view */);
  var vw = new BrowserView(vc, $('body'), 5);
});

