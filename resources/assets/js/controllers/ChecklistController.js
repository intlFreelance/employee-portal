app.controller('ChecklistController', function($scope, $http, $log) {
    $scope.loadModel = loadModel;
    $scope.requiredFieldValid = requiredFieldValid;
    $scope.submitForm = submitForm;
    $scope.states = states;
    $scope.checklist = {};
    $scope.checklist.uploads = [];
    $scope.fileModal = fileModal;
    $scope.uploadFile = uploadFile;
    $scope.deleteFile = deleteFile;
    function loadModel(id){
        $('#uploadModal').on('hidden.bs.modal', function () {
            $scope.uploadType = "";
        })
        $http.get('/employees/get-checklist/'+id).then(function(response){
            var data = response.data;
            if(data.success){
                $scope.checklist = data.checklist;

            }else{
                showSwalError(data.message);
            }
        });
    }
    function requiredFieldValid(form, field){
        return form[field].$invalid  && (!form[field].$pristine || form.$submitted);
    }
    function submitForm(form){
        if(form.$invalid) return;
        $http.post('/employees/save-checklist',$scope.checklist).then(function(response){
            var data = response.data;
            if(data.success){
                $log.info(data);
            }else{
                showSwalError(data.message);
            }
        });
    }
    function fileModal(upload){
        var uploadType = upload.type;
        $scope.uploadType = uploadType;
        $("#uploadModal").modal();
    }
    function uploadFile(form){
        if(!$scope.file){
            form.$invalid = true;
            return;
        }
        form.$invalid = false;
        var fd = new FormData();
        fd.append('file', $scope.file);
        fd.append('uploadType', $scope.uploadType);
        fd.append('id', $scope.checklist.id);
        $http.post('/employees/upload-checklist-file', fd, {
            transformRequest: angular.identity,
            headers: {'Content-Type': undefined}
        }).then(function(response){
            var data = response.data;
            if(data.success){
                for(var i = 0; i <  $scope.checklist.uploads.length; i++){
                    if($scope.checklist.uploads[i].type == $scope.uploadType){
                        $scope.checklist.uploads[i].fileName = data.fileName;
                        break;
                    }
                }
                $("#uploadModal").modal('toggle');
                $scope.checklist.status = data.status;
            }else{
                showSwalError(data.message);
            }
        });

    }
    function deleteFile(upload){
        $scope.uploadType = upload.type;
        swal({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Delete file'
        }).then(function () {
            $http.post('/employees/delete-uploaded-checklist-file', {type:$scope.uploadType, id : $scope.checklist.id})
                .then(function(response){
                    var data = response.data;
                    if(data.success){
                        for(var i = 0; i <  $scope.checklist.uploads.length; i++){
                            if($scope.checklist.uploads[i].type == $scope.uploadType){
                                $scope.checklist.uploads[i].fileName = null;
                                break;
                            }
                        }
                        $scope.checklist.status = data.status;
                        swal('Deleted!', 'Your file has been deleted.', 'success').catch(swal.noop);
                    }else{
                        showSwalError(data.message);
                    }
                }, function (dismiss) {});
        });

    }
    function showSwalError(msg){
        swal(
            'Oops...',
            msg,
            'error'
        ).then(function () {}, function (dismiss) {});
    }
});