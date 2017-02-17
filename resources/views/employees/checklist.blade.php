@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <h2>Employee Checklist</h2>
                <hr/>
                <form  ng-controller="ChecklistController" name="checklistForm" ng-init="loadModel({!! $id !!}, '{!! $source !!}')" novalidate>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="panel-title pull-right">
                                <p class="bg-success" style="color: white;" ng-if="checklist.status=='complete'">&nbsp;<% checklist.status %>&nbsp;</p>
                                <p class="bg-danger" style="color: white;" ng-if="checklist.status!='complete'">&nbsp;<% checklist.status %>&nbsp;</p>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="form-group col-sm-6" ng-class="requiredFieldValid(checklistForm, 'firstName') ? 'has-error' : ''">
                                    <label for="firstName" class="control-label">First Name</label>
                                    <input type="text" name="firstName" ng-model="checklist.firstName"  class="form-control" required placeholder="Enter First Name..."/>
                                    <p ng-show="requiredFieldValid(checklistForm, 'firstName')" class="help-block">First Name is required.</p>
                                </div>
                                <div class="form-group col-sm-6" ng-class="requiredFieldValid(checklistForm, 'lastName') ? 'has-error' : ''">
                                    <label for="lastName" class="control-label">Last Name</label>
                                    <input type="text" name="lastName" ng-model="checklist.lastName"  class="form-control" required placeholder="Enter Last Name..."/>
                                    <p ng-show="requiredFieldValid(checklistForm, 'lastName')" class="help-block">Last Name is required.</p>
                                </div>
                            </div><br/>
                            <div class="row">
                                <ng-form name="addressForm">
                                    <div class="col-sm-12">
                                        <div class="row">
                                            <div class="col-sm-12 form-group" ng-class="addressForm.$invalid  && ((!addressForm.line1.$pristine && !addressForm.city.$pristine && !addressForm.state.$pristine && !addressForm.zip.$pristine) || checklistForm.$submitted) ? 'has-error' : ''">
                                                <label for="billingAddress" class="control-label">Address</label>
                                                <p ng-show="addressForm.$invalid  && ((!addressForm.line1.$pristine && !addressForm.city.$pristine && !addressForm.state.$pristine &&  !addressForm.zip.$pristine) || checklistForm.$submitted)" class="help-block">Address is required.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="row">
                                            <div class="col-sm-6 form-group" ng-class="requiredFieldValid(addressForm, 'line1') ? 'has-error' : ''">
                                                <input type="text" name="line1" ng-model="checklist.address.line1" class="form-control" required placeholder="Line 1"/>
                                            </div>
                                            <div class="col-sm-6 form-group" >
                                                <input type="text" name="line2" ng-model="checklist.address.line2"  class="form-control" placeholder="Line 2"/>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-6 form-group" ng-class="requiredFieldValid(addressForm, 'city')  ? 'has-error' : ''">
                                                <input type="text" name="city" ng-model="checklist.address.city"  class="form-control" required placeholder="City"/>
                                            </div>
                                            <div class="col-sm-3 col-xs-6 form-group" ng-class="requiredFieldValid(addressForm, 'state')  ? 'has-error' : ''">
                                                <select name="state" ng-model="checklist.address.state"  class="form-control" required  ng-options="s as s for s in states"><option value='' disabled>State</option></select>
                                            </div>
                                            <div class="col-sm-3 col-xs-6 form-group" ng-class="requiredFieldValid(addressForm, 'zip') ? 'has-error' : ''">
                                                <input type="text" name="zip" ng-model="checklist.address.zip"  class="form-control" required placeholder="Zip"/>
                                            </div>
                                        </div>
                                    </div>
                                </ng-form>
                            </div><br/>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-sm-12 form-group">
                                            <label for="billingAddress" class="control-label">Uploaded Files</label>
                                        </div>
                                        <div class="col-sm-12">
                                            <table class="table table-striped">
                                                <thead>
                                                <tr><th>Type</th><th>Status</th><th></th></tr>
                                                </thead>
                                                <tbody>
                                                    <tr ng-repeat="upload in checklist.uploads">
                                                        <td><% upload.type %></td>
                                                        <td ng-if="upload.fileName">Uploaded</td>
                                                        <td ng-if="!upload.fileName">Not Uploaded</td>
                                                        <td class="pull-right">
                                                            <a ng-if="upload.fileName" href="/uploads/<% upload.fileName %>" target="_blank" title="Download File" class="btn btn-success btn-sm"><span class="glyphicon glyphicon-download"></span></a>
                                                            <a ng-if="upload.fileName" ng-click="deleteFile(upload)" title="Delete File" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-remove"></span></a>
                                                            <a ng-if="!upload.fileName" ng-click="fileModal(upload)" title="Upload File" class="btn btn-info btn-sm"><span class="glyphicon glyphicon-upload"></span></a>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel-body">
                            <hr/>
                            <div class="row">
                                <div class="col-xs-6 col-sm-3 col-md-3 col-sm-push-6 col-md-push-6 ">
                                    <a href="/{!! $source !!}"  class="btn btn-block btn-default">Cancel</a>
                                </div>
                                <div class="col-xs-6 col-sm-3 col-md-3 col-sm-push-6 col-md-push-6">
                                    <input type="submit" ng-click="submitForm(checklistForm)"  class="btn btn-block btn-primary" value="Save"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="uploadModal" class="modal fade" role="dialog">
                        <div class="modal-dialog">
                            <!-- Modal content-->
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title">Upload file <% uploadType %></h4>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-sm-12 form-group">
                                            <ng-form name="uploadForm" class="form-inline">
                                                <div class="form-group" ng-class="uploadForm.$invalid ? 'has-error' : ''">
                                                    <label class="control-label">File</label>
                                                    <input type="file" id="file" file-model="file" class="form-control" />
                                                </div>
                                                <button type="submit" ng-click="uploadFile(uploadForm)" class="btn btn-primary">Upload</button>
                                                <button type="button" class="btn btn-danger pull-right" data-dismiss="modal">Close</button>
                                            </ng-form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
