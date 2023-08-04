( function( $ ) {
    $(document).ready(function() {
        
        $('.llmspr-select-multiple').select2({
            border: '1px solid #e4e5e7',
        });

        $.fn.select2.amd.require([
            'select2/utils',
            'select2/dropdown',
            'select2/dropdown/attachBody'
          ], function (Utils, Dropdown, AttachBody) {
            function SelectAll() { }
          
            SelectAll.prototype.render = function (decorated) {
              var $rendered = decorated.call(this);
              var self = this;
          
              var $selectAll = $(
                '<button type="button">Select All</button>'
              );
          
              var checkOptionsCount = function()  {
                var count = $('.select2-results__option').length;
                $selectAll.prop('disabled', count > 25);
              }
              
              var $container = $('.select2-container');
              $container.bind('keyup click', checkOptionsCount);
          
              var $dropdown = $rendered.find('.select2-dropdown')
          
              $dropdown.prepend($selectAll);
          
              $selectAll.on('click', function (e) {
                var $results = $rendered.find('.select2-results__option[aria-selected=false]');
                
                // Get all results that aren't selected
                $results.each(function () {
                  var $result = $(this);
                  
                  // Get the data object for it
                  var data = $result.data('data');
                  
                  // Trigger the select event
                  self.trigger('select', {
                    data: data
                  });
                });
                
                self.trigger('close');
              });
              
              return $rendered;
            };
          
            $(".llmspr-select-multiple_user").select2({
              placeholder: "Select User(s)...",
              ajax: {
                      url: ajaxurl, // AJAX URL is predefined in WordPress admin
                      dataType: 'json',
                      delay: 250, // delay in ms while typing when to perform a AJAX search
                      data: function (params) {
                          // $(this).val(null).trigger('change');
                          return {
                              q: params.term, // search query
                              action: 'get_wp_users', // AJAX action for admin-ajax.php
                              page: params.page
                          };
                      },
                      processResults: function (data, params) {
                          params.page = params.page || 1;
          
                          return {
                              results: data.results,
                              pagination: {
                                more: (params.page * 10) < data.count_total
                            }
                        };
                    },
                    cache: true
                },
                minimumInputLength: 3, // the minimum of symbols to input before perform a search
            dropdownAdapter: Utils.Decorate(
              Utils.Decorate(
                Dropdown,
                AttachBody
              ),
              SelectAll
            ),
          });
          $(".llmspr-select-multiple_course").select2({
            placeholder: "Select Course(s)...",
            ajax: {
                    url: ajaxurl, // AJAX URL is predefined in WordPress admin
                    dataType: 'json',
                    delay: 250, // delay in ms while typing when to perform a AJAX search
                    data: function (params) {
                        // $(this).val(null).trigger('change');
                        return {
                            q: params.term, // search query
                            action: 'get_wp_courses', // AJAX action for admin-ajax.php
                            page: params.page
                        };
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;
        
                        return {
                            results: data.results,
                            pagination: {
                              more: (params.page * 10) < data.count_total
                          }
                      };
                  },
                  cache: true
              },
              minimumInputLength: 3, // the minimum of symbols to input before perform a search
          dropdownAdapter: Utils.Decorate(
            Utils.Decorate(
              Dropdown,
              AttachBody
            ),
            SelectAll
          ),
        });
        });

        
                  
        
            
        
        
     $('#llmspr_users').on("select2:select", function (e) {
            var data = e.params.data.text;
            if(data=='All'){
                $("#llmspr_users > option").prop("selected","selected");
                $("#llmspr_users").trigger("change");
            }
            $('#info').html('');
    });

    $('#llmspr_courses').on("select2:select", function (e) {
            var data = e.params.data.text;
            if(data=='All'){
                $("#llmspr_courses > option").prop("selected","selected");
                $("#llmspr_courses").trigger("change");
            }
            $('#info').html('');
        });

        $('#llmspr_memberships').on("select2:select", function (e) {
            var data = e.params.data.text;
            if(data=='All'){
                $("#llmspr_memberships > option").prop("selected","selected");
                $("#llmspr_memberships").trigger("change");
            }
            $('#info').html('');
        });

        $('#llmspr_users_clear').on('click', function() {
            $('#llmspr_users').val(null).trigger('change');
        });

        $('#llmspr_courses_clear').on('click', function() {
            $('#llmspr_courses').val(null).trigger('change');
        });

        $('#llmspr_memberships_clear').on('click', function() {
            $('#llmspr_memberships').val(null).trigger('change');
        });

        $('#llmspr-reset-form').on('submit', function(e) {
            e.preventDefault();

            var users = $('#llmspr_users');
            var courses = $('#llmspr_courses');
            var memberships =$('#llmspr_memberships');

            if( $.isEmptyObject(users.val()) && $.isEmptyObject(courses.val()) && $.isEmptyObject(memberships.val()) ) {
                alert('Please select at least one option');
                return;
            }

            $confirm = confirm('Warning: This will remove all progress data, are you sure?');
            if( !$confirm ) {
                return;
            }

            var $form = $(this);
            var width = 0;

            $.ajax({
                url: ajaxurl,
                data: $form.serialize(),
                type: 'POST',
                beforeSend: function(xhr){
                    $('#llmspr-submit-button').prop('disabled', true);
                    $('#llmspr_progress_container').css('display', 'block');
                    document.getElementById("llmspr_progress_bar").style.width = 0 + "%";
                    $('#llmspr_info').html('');

                    var timer = setInterval(function(){
                        width = width + 1;
                        llmspr_progressbar(width, timer);
                    }, 200);


                },
                success: function(response){
                    if (response.status) {
                        $('#llmspr_info').html(response.data.length + ' of ' + response.data.length + ' User(s)');
                    } else {
                        $('#llmspr_info').html(response.message);
                    }
                },
                complete: function(xhr, textStatus){
                    width = 100;
                    document.getElementById("llmspr_progress_bar").style.width = 100 + "%";
                }
             });

            function llmspr_progressbar(width, timer) {
                if (width >= 100) {
                    clearInterval(timer);
                    $('#llmspr-submit-button').prop('disabled', false);
                    users.val(null).trigger('change');
                    courses.val(null).trigger('change');
                    memberships.val(null).trigger('change');
                } else {
                    width++;
                    document.getElementById("llmspr_progress_bar").style.width = width + "%";
                }
            }

        });

    });
})( jQuery );