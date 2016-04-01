var modalService = angular.module("modalService", []);

modalService.factory("modalService", function($uibModal) { 
  return {
    openModal: function openModal(ctrl, view, size) {
      var modalInstance = $uibModal.open({
        animation: true,
        templateUrl: view,
        scope: ctrl.scope,
        size: size || "md"
      });

      modalInstance.result.then(function(result) {
        ctrl.dismissModal = undefined;
        if (ctrl.verbose)
          console.log("dismissModal reset, modal closed for result")

      }, function(reason) {
        ctrl.dismissModal = undefined;
        if (ctrl.verbose)
          console.log("dismissModal reset, modal dismissed for reason")

      });

      if (ctrl.verbose) {
        console.log("Opened modalInstance from: " + viewPath);
        console.log(modalInstance);
      }

      // A Reference to the dismiss function of the opened modal
      ctrl.dismissModal = modalInstance.dismiss;

      if (ctrl.verbose) {
        console.log("Opening modal, setting ctrl.dismissModal: ");
        console.log(ctrl.dismissModal);
      }
    }
  };
});
