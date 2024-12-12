$(function () {
  'use strict'

  var ticksStyle = {
    fontColor: '#495057',//
    fontStyle: 'bold'
  }
  var  tipos = ["G. Bovino", "G. Ovino", "G. Porcino"];
  var Ingresado = [100, 380, 300];
  var faenado = [320, 260, 380];

  var mode      = 'index'
  var intersect = true

  var $salesChart = $('#sales-chart')
  var salesChart  = new Chart($salesChart, {
    type   : 'bar',
    data   : {
      labels  : tipos,
      datasets: [
        {
          label:"Ingresados",
          backgroundColor: '#007bff',
          borderColor    : '#007bff',
          data           : Ingresado
        },
        {
          label:" Faenados",
          backgroundColor: '#ced4da',
          borderColor    : '#ced4da',
          data           : faenado
        }
      ]
    },
    options: {
      maintainAspectRatio: false,
      tooltips           : {
        mode     : mode,
        intersect: intersect
      },
      hover              : {
        mode     : mode,
        intersect: intersect
      },
      legend             : {
        display: true
      },
      scales             : {
        yAxes: [{
          // display: false,
          gridLines: {
            display      : true,
            lineWidth    : '4px',
            color        : 'rgba(0, 0, 0, .2)',
            zeroLineColor: 'transparent'
          },
          ticks    : $.extend({
            beginAtZero: true,

            // Include a dollar sign in the ticks
            callback: function (value, index, values) {
              if (value >= 1000) {
                value /= 1000
                value += ''
              }
              return  value + ''
            }
          }, ticksStyle)
        }],
        xAxes: [{
          display  : true,
          gridLines: {
            display: true
          },
          ticks    : ticksStyle
        }]
      }
    }
  })
})
