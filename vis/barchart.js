var DATA_LOCATION = "http://localhost:8888/rev_info.json";

function Barchart(config) {
  config                   = config || {};
  this.width               = config.width || 600;
  this.height              = config.height || 600;
  this.transition_duration = config.transition_duration || 1000;
  this.num_ticks           = config.ticks || 10;
  this.bar_width           = config.bar_width || 60;
  this.bar_height          = config.bar_height || 500;

  var self = this;

  this.x = d3.scale.ordinal()
      .rangeRoundBands([0, this.width-70], 0.1);
  this.y = d3.scale.linear()
      .domain([0, 0])
      .rangeRound([this.bar_height, 0]);

  this.create = function() {
    this.chart = d3.select("#chart").append("svg")
        .attr("class", "chart")
        .attr("width", this.width)
        .attr("height", this.height)
      .append("g")
        .attr("transform", "translate(50,25)");


    this.y_axis = d3.svg.axis()
      .scale(this.y)
      .ticks(this.num_ticks)
      .tickSize(0, 0)
      .orient("left");
    this.chart.append("g")
      .attr("class", "y axis")
      // .attr("transform", "translate(-1, 0)")
      .call(this.y_axis);

    this.chart.append("line")
        .attr("class", "base_line")
        .attr("x1", 0)
        .attr("x2", this.width)
        .attr("y1", this.bar_height - .5)
        .attr("y2", this.bar_height - .5)
        .style("stroke", "#000");

  }

  this.update = function(data) {
    console.log(data)

    var max_y = d3.max(data, function(d) { return d.size; });
    this.y.domain([0, max_y])
      .nice();

    this.x.domain(data.map(function(d) { return d.name; }));

    var rect = this.chart.selectAll("rect")
        .data(data, function(d) { return d.name; });


    // Enter…
    rect.enter().insert("rect", ".base_line")
        .attr("x", function(d) { return self.x(d.name); })
        .attr("y", this.bar_height)
        .attr("width", this.x.rangeBand())
        .attr("height", 0)
        .attr("opacity", 1)
      .transition()
        .duration(this.transition_duration)
          .attr("y", function(d) { return self.y(d.size); })
          .attr("height", function(d) { return self.bar_height - self.y(d.size); });

    // Update…
    rect.transition()
        .duration(this.transition_duration)
          .attr("x", function(d) { return self.x(d.name);  })
          .attr("y", function(d) { return self.y(d.size) })
          .attr("width", this.x.rangeBand())
          .attr("height", function(d) { return self.bar_height - self.y(d.size); });

    // Exit…
    rect.exit()
      .transition()
        .duration(this.transition_duration)
        .attr("opacity", 1e-6)
        .attr("height", 0)
        .attr("y", this.bar_height)
        .remove();

    // Labels
    var labels = this.chart.selectAll(".label")
        .data(data, function(d) { return d.name; });
      //.data(data);

    labels.enter().append("text")
        .attr("class", "label")
        .attr("opacity", 1e-6)
        .attr("x", function(d) { return self.x(d.name) + self.x.rangeBand()/2; })
        .attr("y", function(d, i) { return self.bar_height + 12 * (i%2); })
        //.attr("dx", -3) // padding-right
        .attr("dy", "1em") // vertical-align: bottom
        .attr("text-anchor", "middle") // text-align: center
        //.style("fill", "#000")
        .style("font-size", "12")
        .text(function(d) { return d.name; })
          .transition()
            .duration(this.transition_duration)
              .attr("opacity", 1);

    labels.transition()
      .duration(this.transition_duration)
        .attr("opacity", 1)
        .attr("y", function(d, i) { return self.bar_height + 12 * (i%2); })
        .attr("x", function(d) { return self.x(d.name) + self.x.rangeBand()/2; });

    labels.exit()
      .transition()
        .duration(this.transition_duration)
        .attr("opacity", 1e-6)
        .remove();


    // update y-axis
    this.y_axis.tickSize(-this.x.rangeExtent()[1], 0);
    this.chart.select(".y.axis")
      .transition()
        .duration(this.transition_duration)
        .call(this.y_axis);
  }
}

var barchart = new Barchart();
barchart.create();

d3.json(DATA_LOCATION + "?" + new Date().getTime(), function(result){
  console.log(result);
  var index = 0;
  var format = d3.time.format("%X | %x");
  var update = function() {
    if (index < result.length) {
      d3.select("#date").text(format(new Date(result[index].date * 1000)));
      barchart.update(result[index].files);
      index++;

      // var seconds_difference = result[index+1].date - result[index].date;
      // setTimeout(update, seconds_difference * 1000 / 8640);
    }
  };

  // update();

  setInterval(update, 1000);
});

