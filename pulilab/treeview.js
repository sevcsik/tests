(function($){

$.fn.extend({
  treeView: function(data)
  {

    function TreeViewItem(guid, pagename, pos, parentItem)
    {
      function expand(event)
      {
        // expand subtree
        var span = $(event.currentTarget);
        if (span.attr('class') == 'expanded')
        {
          span.attr('class', 'collapsed');
          span.nextAll('ul').hide();
        }
        else
        {
          span.attr('class', 'expanded');
          span.nextAll('ul').show();
        }
      }

      function edit(event) { console.log('not implemented'); };
      function del(event) { console.log('not implemented'); };

      function startDrag(event)
      {
        // get TreeViewItem
        item = $(event.currentTarget).closest('li').data('treeviewitem');

        item.element.addClass('dragging');
        $(document).mousemove(
          {
            'item': item,
          },
          drag
        );

        $(document).mouseup(
          {
            'item': item,
          },
          stopDrag
        );
      }

      function stopDrag(event)
      {
        // get TreeViewItem
        var item = event.data.item;

        // don't trigger mousemove unnessecarily
        $(document).off('mousemove');
        $(document).off('mouseup');

        // regenerate positions and .children[]
        item.parentItem.children = [];
        item.element.parent().children('li').each(function(i, e)
        {
          var currentItem = $(e).data('treeviewitem');
          var previousPosition = currentItem.pos;
          if (previousPosition != i)
          {
            // update position and send ajax post
            currentItem.pos = i;

            $.ajax('dummy_api.html',
            {
              type: 'post',
              data:
              {
                DATA:
                {
                  Guid: currentItem.guid,
                  Pos: i,
                  //Pagename: currentItem.pagename,
                }
              },
              // assume that if it returns 200, it was successful
              success: function()
              {
                item.element.removeClass('dragging');
              }
            });
          }
          item.parentItem.addChild(currentItem);
        });
      }

      function drag(event)
      {
        var item = event.data.item;
        var targetItem = $(event.target).closest('li').data('treeviewitem');

        if (targetItem === undefined) // out of field
          return;

        // don't call .after on ourselves
        // and keep under the same parent
        if (item != targetItem && targetItem.parentItem == item.parentItem)
        {
          // if target is the first item, move to the front
          if (targetItem.element.prevAll('li').length)
            targetItem.element.after(item.element);
          else
            targetItem.element.before(item.element);
        }
      }

      this.guid = guid;
      this.pagename = pagename;
      this.pos = pos;
      this.parentItem = parentItem;
      this.children = [];
      this.dirty = false;

      this.addChild = function(item)
      {
        this.children.push(item);
        if (!this.element.is('.expandable'))
        { // don't add the handler more than once
          this.element.addClass('expandable');
          this.element.find('.pagename').click(expand);
        }
      }

      this.element = $('<li />')
      this.element.data('treeviewitem', this);
      this.element.append(
        $('<span />').addClass('pagename').html(pagename)
      );
      // append controls here
      this.element.append($('<a />').html('move').addClass('move')
        .mousedown(startDrag));
      this.element.append($('<a />').html('edit').addClass('edit')
        .click(edit));
      this.element.append($('<a />').html('delete').addClass('delete')
        .click(del));

      parentItem.addChild(this);
    }

    function TreeView(data, $container)
    {
      this.data = data;
      this.element = $container;
      this.children = [];

      this.addChild = function(item) { this.children.push(item); };

      $container.html('');

      (function generateListItems(data, parentItem)
      {
        var listElement = $('<ul />');

        if (parentItem.pagename !== undefined)
          listElement.addClass('childList');

        $.each(data, function(i, e)
        {
          var listItem = new TreeViewItem(e.Guid, e.Pagename, i, parentItem)
          listElement.append(listItem.element);
          if (e.Pages.length)
            generateListItems(e.Pages, listItem);
        });
        parentItem.element.append(listElement);
      })(data, this);
    }

    if (data === undefined) data = [];

    this.addClass('treeview');
    var treeview = new TreeView(data, this);
    this.data('treeview', treeview);

    return this;
  }
});

})(jQuery);
