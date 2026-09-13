(function ($) {
  "use strict";

  $(document).ready(function () {
    const $container = $(".bdt-ep-future-feature");

    const dataAttributes = {
      frameRate: $container.attr("data-frameRate") !== undefined ? parseInt($container.attr("data-frameRate")) : 150,
      animationTime: $container.attr("data-animationTime") !== undefined ? parseInt($container.attr("data-animationTime")) : 1000,
      stepSize: $container.attr("data-stepSize") !== undefined ? parseInt($container.attr("data-stepSize")) : 100,
      pulseAlgorithm: $container.attr("data-pulseAlgorithm") !== undefined ? parseInt($container.attr("data-pulseAlgorithm")) : 1,
      pulseScale: $container.attr("data-pulseScale") !== undefined ? parseInt($container.attr("data-pulseScale")) : 4,
      pulseNormalize: $container.attr("data-pulseNormalize") !== undefined ? parseInt($container.attr("data-pulseNormalize")) : 1,
      accelerationDelta: $container.attr("data-accelerationDelta") !== undefined ? parseInt($container.attr("data-accelerationDelta")) : 50,
      accelerationMax: $container.attr("data-accelerationMax") !== undefined ? parseInt($container.attr("data-accelerationMax")) : 3,
      keyboardSupport: $container.attr("data-keyboardSupport") !== undefined ? parseInt($container.attr("data-keyboardSupport")) : 1,
      arrowScroll: $container.attr("data-arrowScroll") !== undefined ? parseInt($container.attr("data-arrowScroll")) : 50,
      touchpadSupport: $container.attr("data-touchpadSupport") !== undefined ? parseInt($container.attr("data-touchpadSupport")) : 0,
      fixedBackground: $container.attr("data-fixedBackground") !== undefined ? parseInt($container.attr("data-fixedBackground")) : 1,
      tabletOff: $container.attr("data-tablet-off") !== undefined ? parseInt($container.attr("data-tablet-off")) : 50,
      basicData: $container.attr("data-basicdata") ? JSON.parse($container.attr("data-basicdata")) : {},
    };

    SmoothScroll({
      frameRate: dataAttributes.frameRate,
      animationTime: dataAttributes.animationTime,
      stepSize: dataAttributes.stepSize,
      pulseAlgorithm: dataAttributes.pulseAlgorithm,
      pulseScale: dataAttributes.pulseScale,
      pulseNormalize: dataAttributes.pulseNormalize,
      accelerationDelta: dataAttributes.accelerationDelta,
      accelerationMax: dataAttributes.accelerationMax,
      keyboardSupport: dataAttributes.keyboardSupport,
      arrowScroll: dataAttributes.arrowScroll,
      touchpadSupport: dataAttributes.touchpadSupport,
      fixedBackground: dataAttributes.fixedBackground,
      allowedBrowsers: dataAttributes.basicData.Browsers || []
    });
  });
})(jQuery);
