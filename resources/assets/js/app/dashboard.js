// JavaScript Document
'use strict';
var dashboard = {
	initFilterSwitch: function(){
		$(".switch-options li")
		.on("click", function(){
			$(this)
			.parent()
			.find("li")
			.removeClass("selected")
			.end()
			.end()
			.addClass("selected");
		});
	},
	populateDynamicValues: function(){
		$.ajax({
			url: "/dynamic.json",
			success: function(data){
				$(".dynamic")
				.each(function(){
					var $this = $(this);
					var val = data[$this.data("id")];
					var symbol;
					if(val > 0){
						symbol = "+";
						$this.addClass("text-success");
					}else{
						symbol = "";
						$this.addClass("text-danger");
					}
					
					var fullVal = ($this.is(".noPlus") && symbol === "+" ? "" : symbol)+val+"%";
					
					$this
					.text(fullVal);
				})
			}
		});
	},
	pipeline: {
		init: function(args){
			var w = $(args.containerSelector).parent().innerWidth();
			var h = $(args.containerSelector).parent().innerHeight();
			var barPadding = 20;
			var dataset;
			var barHeight = 30;
			var divider = 7;
			
			d3.json("/"+args.dataFile, function(error, data) {
				if (error) throw error;
			
				dataset = data.data;
				var svgHeight = (dataset.length+1) * (barHeight + barPadding)+barPadding;
				var maxVal = Math.max.apply(null, dataset);
				var minVal = Math.min.apply(null, dataset);

				//Create the SVG elements
				var pipelineBarSvg = d3.select(args.containerSelector)
				.append("svg")
				.attr("width", "100%")
				.attr("viewBox", "0, 0, "+w+", "+svgHeight)
				.attr("class", "pipelineBarSvg");

				pipelineBarSvg.attr("width", w);
				pipelineBarSvg.attr("height", svgHeight);

				//create the background rectangles
				pipelineBarSvg
				.append("g")
				.selectAll("rect")
				.data(dataset)
				.enter()
				.append("rect")
				.attr("x", 0)
				.attr("width", w)
				.attr("y", function(d, i) {
					return i * (h / dataset.length - barPadding) + barPadding;
				})
				.attr("height", barHeight)
				.attr("fill", function(d) {
					return "rgb(177, 180, 186)";
				});

				//create the graph titles
				var titles = data.titles;

				pipelineBarSvg
				.append("g")
				.selectAll("text")
			   .data(titles)
			   .enter()
			   .append("text")
				.text(function(d) {
					return d;
				})
				.attr("x", 0)
				//.transition()
				.attr("y", function(d, i) {
					return i * (h / titles.length - barPadding)+barPadding - 5;
				})
				.attr("font-family", "sans-serif")
				.attr("font-size", "16px")
				.attr("fill", "black");

				//create a colour scale for the bars
				var color = d3.scaleLinear()
				.domain([minVal,maxVal])
				.range(["black", "#6897a7"]);

				pipelineBarSvg
				.append("g")
				.selectAll("rect")
				.data(dataset)
				.enter()
				.append("rect")
				.attr("x", function(d){
					var inc = w / maxVal;
					var pct = (d / maxVal) * 100;
					var barWidth = (w / 100) * pct;
					return (w/2) - (barWidth/2);
				})
				.transition()
				.attr("width", function(d, i){
					var inc = w / maxVal;
					var pct = (d / maxVal) * 100;
					var barWidth = (w / 100) * pct;
					return barWidth;
				})
				//.transition()
				.attr("y", function(d, i) {
					return i * (h / dataset.length - barPadding) + barPadding;
				})
				.attr("height", barHeight)
				//.transition()
				.attr("fill", function(d) {
					return color(d);
				});

				pipelineBarSvg
				.append("g")
				.selectAll("text")
			   .data(dataset)
			   .enter()
			   .append("text")
				.text(function(d) {
					return d;
				})
				.attr("x", function(d, i){
					return (w/2);
				})
				.transition()
				.attr("y", function(d, i) {
					return i * (h / dataset.length - barPadding) + barPadding + 20;
				})
				.attr("font-family", "sans-serif")
				.attr("font-size", "16px")
				.attr("fill", "white")
				.attr("text-anchor", "middle");
				
			});
		}
	},
	gauge: {
		init: function(container, configuration) {
			var that = {};
			var config = {
				size						: 710,
				clipWidth					: 200,
				clipHeight					: 110,
				ringInset					: 20,
				ringWidth					: 5,

				pointerWidth				: 10,
				pointerTailLength			: 5,
				pointerHeadLengthPercent	: 0.9,

				minValue					: 0,
				maxValue					: 10,

				minAngle					: -90,
				maxAngle					: 90,

				transitionMs				: 750,

				majorTicks					: 1,
				labelFormat					: d3.format('d'),
				labelInset					: 10,

				arcColorFn					: d3.interpolateHsl(d3.rgb('#599a51'), d3.rgb('#599a51'))
			};
			
			var range, r, pointerHeadLength, svg, arc, valueArc, scale, ticks, tickData, pointer;
			
			var value = 0;

			var donut = d3.pie();

			function deg2rad(deg) {
				return deg * Math.PI / 180;
			}

			function newAngle(d) {
				var ratio = scale(d);
				var newAngle = config.minAngle + (ratio * range);
				return newAngle;
			}

			function configure(configuration) {
				var prop = undefined;
				for ( prop in configuration ) {
					config[prop] = configuration[prop];
				}

				range = config.maxAngle - config.minAngle;
				r = config.size / 2;
				pointerHeadLength = Math.round(r * config.pointerHeadLengthPercent);

				// a linear scale that maps domain values to a percent from 0..1
				scale = d3.scaleLinear()
						.range([0,1])
						.domain([config.minValue, config.maxValue]);

				ticks = scale.ticks(config.majorTicks);
				tickData = d3.range(config.majorTicks).map(function() {return 1/config.majorTicks;});
				
				//create the background arc
				arc = d3.arc()
				.innerRadius(r - config.ringWidth - config.ringInset)
				.outerRadius(r - config.ringInset)
				.startAngle(function(d, i) {
					var ratio = d * i;
					return deg2rad(config.minAngle + (ratio * range));
				})
				.endAngle(function(d, i) {
					var ratio = d * (i+1);
					return deg2rad(config.minAngle + (ratio * range));
				});
				
				//create the foreground arc based on the given value
				valueArc = d3.arc()
				.innerRadius(r - config.ringWidth - config.ringInset)
				.outerRadius(r - config.ringInset)
				.startAngle(function(d, i) {
					var ratio = d * i;
					return deg2rad(config.minAngle + (ratio * range));
				})
				.endAngle(function(d, i) {
					var ratio = d * (i+1);
					return deg2rad(((180 / 100) * configuration.initialValue) - 90);
				});
			}
			
			that.configure = configure;

			function centerTranslation() {
				return 'translate('+r +','+ r +')';
			}

			function isRendered() {
				return (svg !== undefined);
			}
			that.isRendered = isRendered;
			
			function renderBckground(){
				//this method is run first so creates the SVG container element
				svg = d3.select(container)
				.append('svg:svg')
				.attr('class', 'gauge')
				.attr('width', config.clipWidth)
				.attr('height', config.clipHeight);

				var centerTx = centerTranslation();

				var arcs = svg.append('g')
				.attr('class', 'arc')
				.attr('transform', centerTx);

				arcs.selectAll('path')
				.data(tickData)
				.enter().append('path')
				.attr('fill', "grey")
				.attr('d', arc);
			}

			function render(newValue) {
				var centerTx = centerTranslation();

				var arcs = svg.append('g')
				.attr('class', 'arc')
				.attr('transform', centerTx);

				arcs.selectAll('path')
				.data(tickData)
				.enter().append('path')
				.attr('fill', function(d, i) {
					return config.arcColorFn(d * i);
				})
				.attr('d', valueArc);
				
				//render the value line
				var lineData = [ [config.pointerWidth / 2, 0], 
								[0, -pointerHeadLength],
								[-(config.pointerWidth / 2), 0],
								[0, config.pointerTailLength],
								[config.pointerWidth / 2, 0] ];
				
				var pointerLine = d3.line().curve(d3.curveLinear);
				
				//add it to the SVG
				var pg = svg.append('g').data([lineData])
				.attr('class', 'pointer')
				.attr('transform', centerTx)
				.attr("fill", "#b1b4ba");

				pointer = pg.append('path')
				.attr('d', pointerLine)
				.attr('transform', 'rotate(' +config.minAngle +')');

				//set it's value
				update(newValue === undefined ? 0 : newValue);
			}
			that.render = render;
			that.renderBG = renderBckground;
			
			function update(newValue, newConfiguration) {
				if ( newConfiguration  !== undefined) {
					configure(newConfiguration);
				}
				var ratio = scale(newValue);
				var newAngle = config.minAngle + (ratio * range);
				pointer.transition()
					.duration(config.transitionMs)
					.ease(d3.easeElastic)
					.attr('transform', 'rotate(' +newAngle +')');
				
				d3.select(".totalPctRecruited")
				.text(newValue);
				
				if(newValue > 0){
					d3.select(".totalPctRecruited")
					.attr("class", "totalPctRecruited text-success");
				}else{
					d3.select(".totalPctRecruited")
					.attr("class", "totalPctRecruited text-danger");
				}
			}
			that.update = update;

			configure(configuration);

			return that;
		},
		build: function(args){
			var gauge = this;
			d3.json("/"+args.dataFile, function(error, data) {
				if (error) throw error;
			
				var initialValue = data.value;
				var powerGauge = gauge.init(args.containerSelector, {
					size: 300,
					clipWidth: 300,
					clipHeight: 160,
					ringWidth: 20,
					maxValue: 100,
					transitionMs: 2000,
					initialValue: initialValue
				});

				powerGauge.renderBG();
				powerGauge.render();

				function updateReadings() {
					// just pump in random data here...
					powerGauge.update(initialValue);
				}

				// every few seconds update reading values
				updateReadings();
			});
		}
	},
	barLineGraph: {
		init: function(args){
			// set the dimensions and margins of the graph
			var margin = {top: 20, right: 40, bottom: 40, left: 50},
				width = $(args.containerSelector).innerWidth() - margin.left - margin.right,
				height = $(args.containerSelector).innerHeight() - margin.top - margin.bottom;

			// parse the date / time
			var parseTime = d3.timeParse("%d-%b-%y");
			
			//createthe tooltip holder
			var divTooltip = d3.select(args.containerSelector).append("div").attr("class", "toolTip");

			// set the ranges
			var xBar = d3.scaleBand().range([0, width]).paddingInner(0.5).paddingOuter(0.25);
			var xLine = d3.scalePoint().range([0, width]).padding(0.5);
			var yBar = d3.scaleLinear().range([height, 0]);
			var yLine = d3.scaleLinear().range([height, 0]);

			// define the 1st line
			var valueline = d3.line()
				.x(function(d) { return xLine(d.year); })
				.y(function(d) { return yLine(d.line1); }).curve(d3.curveBasis);
			
			// append the svg obgect to the body of the page
			// appends a 'group' element to 'svg'
			// moves the 'group' element to the top left margin
			var h = height + margin.top + margin.bottom;
			var w = width + margin.left + margin.right;
			var svg = d3.select(args.containerSelector).append("svg")
			.attr("width", w)
			.attr("height", h)
			.attr("width", "100%")
			.attr("viewBox", "0, 0, "+w+", "+h)
			.append("g")
			.attr("transform", "translate(" + margin.left + "," + margin.top + ")");

			// Get the data
			d3.csv("/"+args.dataFile, function(error, data) {
				if (error) throw error;

				// format the data
				data.forEach(function(d) {
				  d.bar = +d.bar;
					d.line1 = +d.line1;
				  d.line2 = +d.line2;
				});

				// Scale the range of the data
				xBar.domain(data.map(function(d) { return d.year; }));
				xLine.domain(data.map(function(d) { return d.year; }));

				yBar.domain([0, d3.max(data, function(d) {
				  return d.bar; 
				})]);

				yLine.domain([0, 100]);

				var rect = svg.selectAll("rect")
				.data(data);
				
				rect.enter()
				.append("rect")
				.merge(rect)
				.attr("class", "bar")
				.style("stroke", "none")
				.style("fill", args.color)
				.attr("x", function(d){ return xBar(d.year); })
				.attr("width", function(d){ return xBar.bandwidth(); })
				.attr("y", function(d){ return yBar(d.bar); })
				.on("mousemove", function(d){
					//populate and show the tooltip while inside the bar
					divTooltip.style("left", d3.event.layerX+10+"px");
					divTooltip.style("top", d3.event.layerY-25+"px");
					divTooltip.style("display", "inline-block");
					var x = d3.event.layerX, y = d3.event.layerY
					var elements = document.querySelectorAll(':hover');
					var l = elements.length -1;
					//l = l-1
					var elementData = elements[l].__data__
					divTooltip.html("<p clas='mbn'><b>"+(d.year)+"</b></p><span class='legendSwatch contractsIn'></span>Contracts In: "+elementData.bar+"<br><span class='legendSwatch pctRecruited'></span>Percentage Recruited: "+elementData.line1+"%");
				})
				.on("mouseout", function(d){
					//hide the tooltip on mouseout
					divTooltip.style("display", "none");
				})
				.transition()
				.attr("height", function(d){
					return height - yBar(d.bar); 
				});
				
				// Add the valueline path.
			  	svg.append("path")
				.data([data])
				.attr("class", "line")
				.style("stroke", "black")
				.transition()
				.attr("d", valueline);
				
				//build the legend
				var w = $(args.containerSelector).innerWidth();
				var h = $(args.containerSelector).innerHeight();
				
				var legend = svg.append("g")
				.attr("class", "legend")
				.attr("height", 100);

				var nodeGroupEnter = legend.append("g").selectAll('rect')
				.data(args.color_hash)
				.enter();
				
				var textWidth = 155;
				
				nodeGroupEnter
				.append("rect")
				.attr("x", function(d, i){ return i *  textWidth;})
				.attr("y", -9)
				.attr("width", 10)
				.attr("height", 10)
				.style("fill", function(d) { 
					var color = d[1];
					return color;
				});
				
				nodeGroupEnter
				.append("text")
				.attr("x", function(d, i){ return i *  textWidth + 15;})
				.attr("y", 0)
				.text(function(d) {
					var text = d[0];
					return text;
				});
					
				legend
				.attr('transform', function(){
					var legendY = h-30;
					var legendX = (w-this.clientWidth)/2;
					return 'translate(0,'+legendY+')';
				});

				// Add the X Axis
				svg.append("g")
				.attr("transform", "translate(0," + height + ")")
				.call(d3.axisBottom(xLine));

				// Add the Y0 Axis
				svg.append("g")
				.attr("class", "barAxis")
				.call(d3.axisLeft(yBar));

				// Add the Y1 Axis
				svg.append("g")
				.attr("class", "lineAxis")
				.attr("transform", "translate( " + width + ", 0 )")
				.call(d3.axisRight(yLine));
			});
		}
	}
};

window.dashboard = dashboard;

dashboard.initFilterSwitch();
dashboard.populateDynamicValues();
dashboard.pipeline.init({
	containerSelector: ".pipeline-wrapper",
	dataFile: "pipeline.json"
});

dashboard.gauge.build({
	containerSelector: ".tpr_graph",
	dataFile: "gauge.json"
});
dashboard.barLineGraph.init({
	containerSelector: ".rrvci", 
	color: "#116682",
	color_hash: [  
	  ["Contracts In", "#116682"],
	  ["Percentage Recruited", "#000000"]
	],
	dataFile: 'data4.csv'
});
dashboard.barLineGraph.init({
	containerSelector: ".rrvo", 
	color: "#6897a7",
	color_hash: [  
	  ["Openings", "#6897a7"],
	  ["Percentage Recruited", "#000000"]
	],
	dataFile: 'data4.csv'
});