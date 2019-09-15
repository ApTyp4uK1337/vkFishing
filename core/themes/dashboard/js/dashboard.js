
$(function(){
  'use strict'

  var area1 = new Chartist.Line('#chartArea1', {
    labels: [1, 2, 3, 4, 5, 6, 7, 8],
    series: [
      [6, 8, 7, 10, 14, 11, 16, 18],
      [2, 4, 3, 4, 5, 3, 5, 4]
    ]
  }, {
    high: 30,
    low: 0,
    axisY: {
      onlyInteger: true,
      offset: 0
    },
    axisX: {
      offset: 0
    },
    showArea: true,
    fullWidth: true,
    chartPadding: {
      bottom: 0,
      left: 0,
      right: 0,
      top: 0
    }
  });

  // resize chart when container changest it's width
  new ResizeSensor($('.dash-chartist'), function(){
    area1.update();
  });


  var multibar = new Rickshaw.Graph({
    element: document.querySelector('#chartMultiBar1'),
    renderer: 'bar',
    stack: false,
    max: 60,
    series: [{
      data: [
        { x: 0, y: 20 },
        { x: 1, y: 25 },
        { x: 2, y: 10 },
        { x: 3, y: 20 },
        { x: 4, y: 15 },
        { x: 5, y: 18 },
        { x: 6, y: 15 },
        { x: 7, y: 3 },
        { x: 8, y: 2 },
        { x: 9, y: 5 },
        { x: 10, y: 3 },
        { x: 11, y: 2 },
        { x: 12, y: 4 },
        { x: 13, y: 5 },
        { x: 14, y: 1 },
        { x: 15, y: 2 }
      ],
      color: '#8AC6E8'
    },
    {
      data: [
        { x: 0, y: 10 },
        { x: 1, y: 30 },
        { x: 2, y: 45 },
        { x: 3, y: 30 },
        { x: 4, y: 25 },
        { x: 5, y: 15 },
        { x: 6, y: 10 },
        { x: 7, y: 4 },
        { x: 8, y: 3 },
        { x: 9, y: 2 },
        { x: 10, y: 5 },
        { x: 11, y: 2 },
        { x: 12, y: 3 },
        { x: 13, y: 2 },
        { x: 14, y: 4 },
        { x: 15, y: 5 }
      ],
      color: '#1B84E7'
    }]
  });
  multibar.render();

  // Responsive Mode
  new ResizeSensor($('.slim-mainpanel'), function(){
    multibar.configure({
      width: $('#chartMultiBar1').width(),
      height: $('#chartMultiBar1').height()
    });
    multibar.render();
  });

  $('#sparkline3').sparkline('html', {
    width: '100%',
    height: '45',
    lineColor: '#0083CD',
    fillColor: 'rgba(0,131,205,0.2)',
  });

  $('#sparkline4').sparkline('html', {
    width: '100%',
    height: '45',
    lineColor: '#0D0D6B',
    fillColor: 'rgba(13,13,107,0.2)'
  });

  var rs3 = new Rickshaw.Graph({
    element: document.querySelector('#rs3'),
    renderer: 'line',
    series: [{
      data: [
        { x: 0, y: 5 },
        { x: 1, y: 7 },
        { x: 2, y: 10 },
        { x: 3, y: 11 },
        { x: 4, y: 12 },
        { x: 5, y: 10 },
        { x: 6, y: 9 },
        { x: 7, y: 7 },
        { x: 8, y: 6 },
        { x: 9, y: 8 },
        { x: 10, y: 9 },
        { x: 11, y: 10 },
        { x: 12, y: 7 },
        { x: 13, y: 10 }
      ],
      color: '#1B84E7',
    }]
  });
  rs3.render();

});
