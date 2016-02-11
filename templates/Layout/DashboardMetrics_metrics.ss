<div class="content page-header">
    <div class="row">
        <div class="col-md-12">
            <% include Breadcrumb %>
            <% include DeploymentTabs %>
            <% include ProjectLinks %>
        </div>
    </div>
</div>

<div class="content">

    <div class="metric-set">
        <div class="metrics">
        <% if $Metrics %>

        <div id="rangeheader">
            <div id="rangedropdown">
                <div id="picker">$Range</div>
                <a class="btn btn-primary">
                    <i class="fa fa-clock-o"></i>
                 Set Range
                </a>
            </div>
        </div>

        <% loop $Metrics %>
            <h3>$Name</h3>
            <div class="metrics">
                <div class="metric">
                    <div height="200" class="chart requests" data-metric data-display="$ChartType" data-points='$Up.getData($ID)'></div>
                    <p>$Description</p>
                </div>
                <hr>
        <% end_loop %>

        <% else %>
        <h3>No Metrics to display!</h3>
        <% end_if %>

        </div>
    </div>
</div>


<% require css('deploynaut-metrics/css/vendor/c3.min.css') %>
<% require css('deploynaut-metrics/css/metrics.css') %>
<% require javascript('deploynaut-metrics/javascript/vendor/moment.js') %>
<% require javascript('deploynaut-metrics/javascript/vendor/d3.min.js') %>
<% require javascript('deploynaut-metrics/javascript/vendor/c3.min.js') %>
<% require javascript('deploynaut-metrics/javascript/metrics.js') %>
<% require javascript('deploynaut-metrics/javascript/timepicker.js') %>
