<?= $this->extend('layouts/layout') ?>

<?= $this->section('styles') ?>
<style>
    .list-item:hover {
        background-color: rgb(240, 243, 245) !important;
        transition: .25s ease-in-out all;
        border-radius: 1000px;
    }
    .delete {
        background-color: #f00 !important;
        color: #fff;
    }
    .delete button {
        background-color: #fff;
    }
    th,
    td,
    td input{
        width: 100px !important;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="card" id="cems">
    <div class="card-body p-5">

        <div class="row">
            <div class="col-12" id="formCems">
                <div class="row">
                    <div class="col-md-12">
                        <h3>Profile</h3>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group row">
                            <label class="col-md-4 col-form-label">Name</label>
                            <div class="col-md-8">
                                <input <?= in_array("WEB.UPDATE.CEMS", session()->get('role')) ? '' : 'disabled' ?> class="form-control" name="name" type="text" placeholder="Cems Name" firstValue="<?= $cems->name; ?>" value="<?= isset($cems->name) ? $cems->name : "" ?>">
                            </div>
                        </div>
                        <?php if (isset($cems->group)):?>
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label">Group</label>
                                <div class="col-md-8">
                                    <input <?= in_array("WEB.UPDATE.CEMS", session()->get('role')) ? '' : 'disabled' ?> class="form-control" name="group" type="text" placeholder="Cems Group" firstValue="<?= $cems->group; ?>" value="<?= isset($cems->group) ? $cems->group : "" ?>">
                                </div>
                            </div>
                        <?php endif?>
                        <div class="form-group row">
                            <label class="col-md-4 col-form-label">Lat. & Long.</label>
                            <div class="col-md-8 d-flex flex-row">
                                <input <?= in_array("WEB.UPDATE.CEMS", session()->get('role')) ? '' : 'disabled' ?> class="form-control mr-2" name="latitude" type="number" placeholder="Longitude" firstValue="<?= $cems->latitude; ?>" value="<?= isset($cems->latitude) ? $cems->latitude : "" ?>">
                                <input <?= in_array("WEB.UPDATE.CEMS", session()->get('role')) ? '' : 'disabled' ?> class="form-control" name="longitude" type="number" placeholder="Latitude" firstValue="<?= $cems->longitude; ?>" value="<?= isset($cems->longitude) ? $cems->longitude : "" ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-4 col-form-label">Dimension</label>
                            <div class="col-md-8">
                                <input <?= in_array("WEB.UPDATE.CEMS", session()->get('role')) ? '' : 'disabled' ?> class="form-control" name="dimension" type="text" placeholder="0 m x 0 m" firstValue="<?= $cems->dimension; ?>" value="<?= isset($cems->dimension) ? $cems->dimension : "" ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-4 col-form-label">Fuel</label>
                            <div class="col-md-8">
                                <input <?= in_array("WEB.UPDATE.CEMS", session()->get('role')) ? '' : 'disabled' ?> class="form-control" name="fuel" type="text" placeholder="Batu Bara" firstValue="<?= $cems->fuel; ?>" value="<?= isset($cems->fuel) ? $cems->fuel : "" ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-4 col-form-label">Status</label>
                            <div class="col-md-8">
                                <div class="btn-group btn-group-toggle mx-3" data-toggle="buttons">
                                    <label class="btn btn-outline-success <?= $cems->status == "Running" ? "active" : "" ?>">
                                        <input <?= in_array("WEB.UPDATE.CEMS", session()->get('role')) ? '' : 'disabled readonly' ?> id="option1" type="radio" name="status" label="Running" autocomplete="off" <?= $cems->status == "Running" ? "checked" : "" ?>> Running
                                    </label>
                                    <label class="btn btn-outline-warning <?= $cems->status == "Maintenance" ? "active" : "" ?>">
                                        <input <?= in_array("WEB.UPDATE.CEMS", session()->get('role')) ? '' : 'disabled readonly' ?> id="option2" type="radio" name="status" label="Maintenance" autocomplete="off" <?= $cems->status == "Maintenance" ? "checked" : "" ?>> Maintenance
                                    </label>
                                    <label class="btn btn-outline-danger <?= $cems->status == "Shutdown" ? "active" : "" ?>">
                                        <input <?= in_array("WEB.UPDATE.CEMS", session()->get('role')) ? '' : 'disabled readonly' ?> d="option3" type="radio" name="status" label="Shutdown" autocomplete="off" <?= $cems->status == "Shutdown" ? "checked" : "" ?>> Shutdown
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-4 col-form-label">Show in Dashboard</label>
                            <div class="col-md-8">
                                <div class="btn-group btn-group-toggle mx-3" data-toggle="buttons">
                                    <label class="btn btn-outline-success <?= $cems->show_dashboard == "1" ? "active" : "" ?>">
                                        <input <?= in_array("WEB.UPDATE.CEMS", session()->get('role')) ? '' : 'disabled readonly' ?> value="1" type="radio" name="show_dashboard" label="Show" autocomplete="off" <?= $cems->show_dashboard == "1" ? "checked" : "" ?>> Show
                                    </label>
                                    <label class="btn btn-outline-warning <?= $cems->show_dashboard != "1" ? "active" : "" ?>">
                                        <input <?= in_array("WEB.UPDATE.CEMS", session()->get('role')) ? '' : 'disabled readonly' ?> value="0" type="radio" name="show_dashboard" label="Hide" autocomplete="off" <?= $cems->show_dashboard == "0" ? "checked" : "" ?>> Hide
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-4 col-form-label">Use Shutdown Scheduling</label>
                            <div class="col-md-8">
                                <div class="ckbx-style-13">
                                    <input <?=  in_array("WEB.UPDATE.CEMS", session()->get('role')) ? ($schedule && intval($schedule->executed) == 0 ? 'disabled checked' : '') : 'disabled' ?> type="checkbox" id="use_unit_scheduling" name="use_unit_scheduling" onchange="set_unit_scheduling()">
                                    <label for="use_unit_scheduling"></label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row <?= $schedule ? (intval($schedule->executed) == 0 ? '' : 'd-none') : 'd-none' ?>" id="unit_scheduling_container">
                            <label class="col-md-4 col-form-label">Pick Date</label>
                            <div class="col-md-8" <?= $schedule ? $schedule->hour : '' ?>>
                                <input type="text" class="form-control <?= $schedule ? '' : 'datepicker' ?>" id="unit_scheduling" disabled value="<?= $schedule ? $schedule->hour : '' ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group row">
                            <div id="map" style="height: 300px; width: 100%; border-radius: 15px;"></div>
                        </div>
                    </div>

                    <?php if(in_array("WEB.UPDATE.CEMS", session()->get('role'))): ?>
                        <div class="col-md-12 my-3">
                            <div class="form-group" >
                                <button class="btn btn-success mr-4" onclick="saveOnlyCems()">
                                    <i class="fa fa-check"></i>
                                    <span>Save</span>
                                </button>
                                <button class="btn btn-danger mr-4" onclick="deleteCems()">
                                    <i class="fa fa-exclamation-triangle"></i>
                                    <span>Delete Data</span>
                                </button>
                                <button class="btn btn-primary" onclick="resetMarker();" style="display: none; transition: all .25s ease;" id="btnResetMarker">
                                    <i class="fa fa-undo"></i>
                                    <span>Reset Map</span>
                                </button>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="col-md-12 mt-5">
                        <h3>Shutdown History</h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-striped table-hover" id="tableShutdown" style="width: 100% !important ;">
                            <thead>
                                <tr class="text-capitalize">
                                    <th>timestamp</th>
                                    <th>schedule date</th>
                                    <th>executed</th>
                                    <th>times executed</th>
                                    <th>shutdown end</th>
                                    <th>Detail</th>
                                </tr>
                            </thead>
                        </table>
                    </div>


                    <div class="col-md-12 mt-5">
                        <h3>Parameter List</h3>
                    </div>
                    
                    <?php if(in_array("WEB.UPDATE.CEMS.PARAMETER", session()->get('role'))): ?>
                        <div class="col-md-6 mb-3">
                            <div class="my-auto">
                                <?php if(in_array("WEB.UPDATE.CEMS.PARAMETER.SEND.KLHK.ALL", session()->get('role'))):?>
                                    <button data-toggle="tooltip" data-placement="top" title="Set All Parameter to Send to KLHK" class="btn btn-success btn-sm rounded-pill" onclick="setSwitch(12)">
                                        <i class="fa fa-link"></i>
                                        Send All to KLHK
                                    </button>
                                <?php endif;?>
                                <?php if(in_array("WEB.UPDATE.CEMS.PARAMETER.MAINTENANCE.ALL", session()->get('role'))):?>
                                    <button data-toggle="tooltip" data-placement="top" title="Set All Parameter to Maintenance" class="btn btn-warning btn-sm rounded-pill" onclick="setSwitch(13)">
                                        <i class="fa fa-exclamation-triangle"></i>
                                        Set All Maintenance
                                    </button>
                                <?php endif;?>
                                <!-- <?php if(count($cemsParameter) == 0):?>
                                    <button data-toggle="tooltip" data-placement="top" title="Copy Configured Parameter from Other Cems" class="btn btn-info btn-sm rounded-pill" onclick="openModalCopy()">
                                        <i class="fa fa-exclamation-triangle"></i>
                                        Copy Parameter
                                    </button>
                                <?php endif;?> -->
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="my-auto float-lg-right">
                                <?php if(in_array("WEB.UPDATE.CEMS.PARAMETER", session()->get('role'))):?>
                                    <button class="btn btn-success btn-sm rounded-pill" onclick="saveOnlyParameter()">Save Parameter</button>
                                <?php endif;?>
                                <?php if(in_array("WEB.ADD.CEMS.PARAMETER", session()->get('role'))):?>
                                    <button class="btn btn-primary btn-sm rounded-pill" onclick="newRowParameter()">Add Parameter</button>
                                <?php endif;?>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <div class="col-md-12">
                                <table class="table table-sm table-bordered table-striped table-hover" id="tableParameter" style="width: 100% !important ;">
                                    <thead>
                                        <tr style="white-space: nowrap;">
                                            <th class="text-center" rowspan="2">Parameter</th>
                                            <th class="text-center" rowspan="2">Source</th>
                                            <th class="text-center" colspan="3">Terukur</th>
                                            <th class="text-center" colspan="3">Terkoreksi</th>
                                            <th class="text-center" rowspan="2">CodeVal</th>
                                            <th class="text-center" rowspan="2">Formula</th>
                                            <th class="text-center" rowspan="2">Desc</th>
                                            <th class="text-center" rowspan="2">Status</th>
                                            <th class="text-center" rowspan="2">Send KLHK</th>
                                            <th class="text-center" rowspan="2">KLHK Code</th>
                                            <th class="text-center" rowspan="2">Maintenance</th>
                                            <th class="text-center" rowspan="2">Maintenance Description</th>
                                            <!-- <th class="text-center" rowspan="2">Maintenance Scheduling</th> -->
                                            <th class="text-center" rowspan="2">Option</th>
                                        </tr>
                                        <tr style="white-space: nowrap;">
                                            <th class="text-center">High</th>
                                            <th class="text-center">High-High</th>
                                            <th class="text-center">UoM</th>
                                            <th class="text-center">High</th>
                                            <th class="text-center">High-High</th>
                                            <th class="text-center">UoM</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($cemsParameter as $row) { ?>
                                            <tr add-update="update" isChange="0" data-id="<?= $row->cemsParameterId; ?>" data-active="<?= $row->active ?>">
                                                <td><input <?= in_array("WEB.UPDATE.CEMS.PARAMETER.NAME", session()->get('role')) ? '' : 'disabled readonly' ?> type="text" name="parameterName[]" placeholder="Type here" class="input-transparent" firstValue="<?= $row->parameterName; ?>" value="<?= $row->parameterName; ?>" /></td>
                                                <td><input <?= in_array("WEB.UPDATE.CEMS.PARAMETER.SOURCE", session()->get('role')) ? '' : 'disabled readonly' ?> type="text" name="source[]" placeholder="Type here" class="input-transparent" firstValue="<?= $row->source; ?>" value="<?= $row->source; ?>" /></td>
                                                <td><input <?= in_array("WEB.UPDATE.CEMS.PARAMETER.HIGH.TERUKUR", session()->get('role')) ? '' : 'disabled readonly' ?> style="max-width: 100px;" type="number" name="high_terukur[]" placeholder="Type here" class="input-transparent" firstValue="<?= $row->high_terukur; ?>" value="<?= $row->high_terukur; ?>" /></td>
                                                <td><input <?= in_array("WEB.UPDATE.CEMS.PARAMETER.HIGH.HIGH.TERUKUR", session()->get('role')) ? '' : 'disabled readonly' ?> style="max-width: 100px;" type="number" name="highHigh_terukur[]" placeholder="Type here" class="input-transparent" firstValue="<?= $row->highHigh_terukur; ?>" value="<?= $row->highHigh_terukur; ?>" /></td>
                                                <td><input <?= in_array("WEB.UPDATE.CEMS.PARAMETER.UOM.TERUKUR", session()->get('role')) ? '' : 'disabled readonly' ?> type="text" name="uom_terukur[]" placeholder="Type here" class="input-transparent" firstValue="<?= $row->uom_terukur; ?>" value="<?= $row->uom_terukur; ?>" /></td>
                                                <td><input <?= in_array("WEB.UPDATE.CEMS.PARAMETER.HIGH.TERKOREKSI", session()->get('role')) ? '' : 'disabled readonly' ?> style="max-width: 100px;" type="number" name="high_terkoreksi[]" placeholder="Type here" class="input-transparent" firstValue="<?= $row->high_terkoreksi; ?>" value="<?= $row->high_terkoreksi; ?>" /></td>
                                                <td><input <?= in_array("WEB.UPDATE.CEMS.PARAMETER.HIGH.HIGH.TERKOREKSI", session()->get('role')) ? '' : 'disabled readonly' ?> style="max-width: 100px;" type="number" name="highHigh_terkoreksi[]" placeholder="Type here" class="input-transparent" firstValue="<?= $row->highHigh_terkoreksi; ?>" value="<?= $row->highHigh_terkoreksi; ?>" /></td>
                                                <td><input <?= in_array("WEB.UPDATE.CEMS.PARAMETER.UOM.TERKOREKSI", session()->get('role')) ? '' : 'disabled readonly' ?> type="text" name="uom_terkoreksi[]" placeholder="Type here" class="input-transparent" firstValue="<?= $row->uom_terkoreksi; ?>" value="<?= $row->uom_terkoreksi; ?>" /></td>
                                                <td><input <?= in_array("WEB.UPDATE.CEMS.PARAMETER.CODEVAL", session()->get('role')) ? '' : 'disabled readonly' ?> disabled readonly type="number" name="codeVal[]" placeholder="Type here" class="input-transparent" firstValue="<?= $row->codeVal; ?>" value="<?= $row->codeVal; ?>" /></td>
                                                <td><input <?= in_array("WEB.UPDATE.CEMS.PARAMETER.FORMULA", session()->get('role')) ? '' : 'disabled readonly' ?> type="text" name="formula[]" placeholder="Type here" class="input-transparent" firstValue="<?= $row->formula; ?>" value="<?= $row->formula; ?>" /></td>
                                                <td><input <?= in_array("WEB.UPDATE.CEMS.PARAMETER.DESC", session()->get('role')) ? '' : 'disabled readonly' ?> type="text" name="desc[]" placeholder="Type here" class="input-transparent" firstValue="<?= $row->desc; ?>" value="<?= $row->desc; ?>" /></td>
                                                <td><span><?= $row->active == 1 ? 'active' : 'non-active' ?></span></td>
                                                <td>
                                                    <div class="ckbx-style-13">
                                                        <input <?= in_array("WEB.UPDATE.CEMS.PARAMETER.SEND.KLHK", session()->get('role')) ? '' : 'disabled readonly' ?> type="checkbox" firstValue="<?= $row->send_klhk; ?>" id="check_<?= $row->cemsParameterId ?>" <?= $row->send_klhk == "1" ? "checked" : "" ?> name="send_klhk[]">
                                                        <label for="check_<?= $row->cemsParameterId ?>"></label>
                                                    </div>
                                                </td>
                                                <td><input type="text" name="klhk_code[]" placeholder="Type here" class="input-transparent" firstValue="<?= $row->klhk_code; ?>" value="<?= $row->klhk_code; ?>" /></td>
                                                <td>
                                                    <div class="ckbx-style-13">
                                                        <input onchange="document.querySelector('#maintenance_description_<?= $row->cemsParameterId ?>').disabled = document.querySelector('#maintenance_<?= $row->cemsParameterId ?>').checked ? false : true" <?= in_array("WEB.UPDATE.CEMS.PARAMETER.MAINTENANCE", session()->get('role')) ? '' : 'disabled readonly' ?> type="checkbox" firstValue="<?= $row->maintenance; ?>" id="maintenance_<?= $row->cemsParameterId ?>" <?= $row->maintenance == "1" ? "checked" : "" ?> name="maintenance[]">
                                                        <label for="maintenance_<?= $row->cemsParameterId ?>"></label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group mb-0">
                                                        <select <?= $row->maintenance == "1" ? "" : "disabled" ?> class="form-control" id="maintenance_description_<?= $row->cemsParameterId ?>" value="<?= $row->maintenance_description; ?>" firstValue="<?= $row->maintenance_description; ?>" name="maintenance_description[]">
                                                            <option <?= $row->maintenance_description == null ? 'Selected' : ''; ?> value="-">Set Description</option>
                                                            <option <?= $row->maintenance_description == 'DAS Fault' ? 'Selected' : ''; ?> value="DAS Fault">DAS Fault</option>
                                                            <option <?= $row->maintenance_description == 'Sensor Fault' ? 'Selected' : ''; ?> value="Sensor Fault">Sensor Fault</option>
                                                            <option <?= $row->maintenance_description == 'Connection Fault' ? 'Selected' : ''; ?> value="Connection Fault">Connection Fault</option>
                                                            <option <?= $row->maintenance_description == 'Analyzer Fault' ? 'Selected' : ''; ?> value="Analyzer Fault">Analyzer Fault</option>
                                                            <option <?= $row->maintenance_description == 'Calibration' ? 'Selected' : ''; ?> value="Calibration">Calibration</option>
                                                        </select>
                                                    </div>
                                                </td>
                                                <!-- <td>
                                                    <div class="ckbx-style-13">
                                                        <input <?= in_array("WEB.UPDATE.CEMS.PARAMETER.MAINTENANCE.SCHEDULE", session()->get('role')) ? '' : 'disabled readonly' ?> type="checkbox" firstValue="<?= $row->maintenance_scheduling; ?>" id="maintenance_scheduling<?= $row->cemsParameterId ?>" <?= $row->maintenance_scheduling == "1" ? "checked" : "" ?> name="maintenance_scheduling[]">
                                                        <label for="maintenance_scheduling<?= $row->cemsParameterId ?>"></label>
                                                    </div>
                                                </td> -->
                                                <td class="text-center">
                                                    <button <?= in_array("WEB.UPDATE.CEMS.PARAMETER.STATUS", session()->get('role')) ? '' : 'disabled readonly' ?>  onclick="removeRestoreRowParameter(this)" class="btn btn-sm rounded-pill  removeRestoreRow  d-flex flex-row <?= $row->active == 0 ? 'btn-success' : 'btn-danger' ?>"><i class="fa fa-times my-auto mr-2"></i> <span><?= $row->active == 0 ? 'activate' : 'deactivate' ?></span></button>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                        <?php if(in_array("WEB.ADD.CEMS.PARAMETER", session()->get('role'))):?>
                                            <tr add-update="add" data-id="0">
                                                <td><input type="text" name="parameterName[]" placeholder="Type here" class="input-transparent" value="" /></td>
                                                <td><input type="text" name="source[]" placeholder="Type here" class="input-transparent" value="" /></td>
                                                <td style="max-width: 60px !important"><input type="number" name="high_terukur[]" placeholder="Type here" class="input-transparent" value="" /></td>
                                                <td style="max-width: 60px !important"><input type="number" name="highHigh_terukur[]" placeholder="Type here" class="input-transparent" value="" /></td>
                                                <td><input type="text" name="uom_terukur[]" placeholder="Type here" class="input-transparent" value="" /></td>
                                                <td style="max-width: 60px !important"><input type="number" name="high_terkoreksi[]" placeholder="Type here" class="input-transparent" value="" /></td>
                                                <td style="max-width: 60px !important"><input type="number" name="highHigh_terkoreksi[]" placeholder="Type here" class="input-transparent" value="" /></td>
                                                <td><input type="text" name="uom_terkoreksi[]" placeholder="Type here" class="input-transparent" value="" /></td>
                                                <td style="max-width: 60px !important"><input disabled readonly type="number" name="codeVal[]" placeholder="Type here" class="input-transparent" value="<?= $cemsParameter ? end($cemsParameter)->codeVal+=1 : 0 ?>" /></td>
                                                <td><input type="text" name="formula[]" placeholder="Type here" class="input-transparent" value="" /></td>
                                                <td><input type="text" name="desc[]" placeholder="Type here" class="input-transparent" value="" /></td>
                                                <td style="max-width: 80px;">
                                                    <span>auto-active</span>
                                                </td>
                                                <td style="min-width: 100px !important;">
                                                    <div class="ckbx-style-13">
                                                        <input type="checkbox" id="check_<?= $cemsParameter ? end($cemsParameter)->cemsParameterId+=1 : 0 ?>" name="send_klhk[]">
                                                        <label for="check_<?= $cemsParameter ? end($cemsParameter)->cemsParameterId+=1 : 0 ?>"></label>
                                                    </div>
                                                </td>
                                                <td><input type="text" name="klhk_code[]" placeholder="Type here" class="input-transparent" value="" /></td>
                                                <td style="min-width: 80px !important;">
                                                    <div class="ckbx-style-13">
                                                        <input type="checkbox" id="maintenance_<?= $cemsParameter ? end($cemsParameter)->cemsParameterId+=1 : 0 ?>" name="maintenance[]">
                                                        <label for="maintenance_<?= $cemsParameter ? end($cemsParameter)->cemsParameterId+=1 : 0 ?>"></label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group mb-0">
                                                        <select class="form-control" id="maintenance_description_<?= $cemsParameter ? end($cemsParameter)->cemsParameterId+=1 : 0 ?>" name="maintenance_description[]">
                                                            <option value="-">Set Description</option>
                                                            <option value="DAS Fault">DAS Fault</option>
                                                            <option value="Sensor Fault">Sensor Fault</option>
                                                            <option value="Connection Fault">Connection Fault</option>
                                                            <option value="Analyzer Fault">Analyzer Fault</option>
                                                            <option value="Calibration">Calibration</option>
                                                        </select>
                                                    </div>
                                                </td>
                                                <!-- <td style="min-width: 80px !important;">
                                                    <div class="ckbx-style-13">
                                                        <input type="checkbox" id="maintenance_scheduling<?= $cemsParameter ? end($cemsParameter)->cemsParameterId+=1 : 0 ?>" name="maintenance_scheduling[]">
                                                        <label for="maintenance_scheduling<?= $cemsParameter ? end($cemsParameter)->cemsParameterId+=1 : 0 ?>"></label>
                                                    </div>
                                                </td> -->
                                                <td class="text-center">
                                                    <button onclick="removeRestoreRowParameter(this)" class="btn btn-sm rounded-pill btn-danger  removeRestoreRow  d-flex flex-row"><i class="fa fa-times my-auto mr-2"></i> <span>delete</span></button>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="modalCEMS" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                        Copy Configured Parameters from Other CEMS
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body modal-sync">
                    <div class="p-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6" id="cardCems"></div>
                                    <div class="col-md-6" id="cardParameter">
                                        <h6>Parameter List</h6>
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item">Select CEMS first</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn btn-success float-right" onclick="copyCems()">Save</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>

<script>
    mapboxgl.accessToken = '<?= env('MB_TOKEN') ?>'
    let updated = false
    let CEMS = JSON.parse('<?php echo json_encode($cemsList); ?>').filter(x => x.cemsId != <?= $cems->cemsId ?>);
    const masterParameter = JSON.parse('<?= json_encode($cemsParameter) ?>');
    const cur_lng = document.querySelector('[name="longitude"]').value;
    const cur_lat = document.querySelector('[name="latitude"]').value;
    let LngLat = [
        parseFloat(<?php echo $cems->longitude ?? 0 ?>),
        parseFloat(<?php echo $cems->latitude ?? 0 ?>),
    ];
    let map = new mapboxgl.Map({
        container: 'map',
        style: 'mapbox://styles/mapbox/streets-v11',
        zoom: 7,
        center: LngLat
    });
    let marker = new mapboxgl.Marker({
        draggable: true
    })
    .setLngLat(LngLat)
    .addTo(map)
    marker.on('dragend', () => {
        const new_LngLat = marker.getLngLat();
        document.querySelector('[name="latitude"]').value = new_LngLat.lat
        document.querySelector('[name="longitude"]').value = new_LngLat.lng

        if((cur_lat != new_LngLat.lat) && (cur_lng != new_LngLat.lng)){
            $('#btnResetMarker').show();
        }else{
            $('#btnResetMarker').hide();
        }
    })

    function resetMarker(){
        document.querySelector('[name="latitude"]').value = cur_lat
        document.querySelector('[name="longitude"]').value = cur_lng
        $('#btnResetMarker').hide();
        marker
        .setLngLat(LngLat)
        .addTo(map)
    }

    function set_unit_scheduling(){
        document.querySelector('[name="use_unit_scheduling"]').checked == true ?
        document.querySelector('#unit_scheduling_container').classList.remove('d-none') : 
        document.querySelector('#unit_scheduling_container').classList.add('d-none')

        document.querySelector('[name="use_unit_scheduling"]').checked == true ?
        document.querySelector('#unit_scheduling').disabled = false : 
        document.querySelector('#unit_scheduling').disabled = true
    }

    $(() => {
        $('.datepicker').daterangepicker({
            singleDatePicker: true,
            timePicker: true,
            timePicker24Hour: true,
            startDate: moment().add("1", "minutes"),
            minDate: moment().add("1", "minutes"),
            locale: {
                format: 'YYYY-MM-DD HH:mm'
            }
        });
        $('#tableParameter input[name="parameterName[]"], #tableParameter input[name="source[]"],#tableParameter input[name="high_terukur[]"], #tableParameter input[name="highHigh_terukur[]"],#tableParameter input[name="high_terkoreksi[]"], #tableParameter input[name="highHigh_terkoreksi[]"], #tableParameter input[name="codeVal[]"], #tableParameter input[name="uom_terukur[]"], #tableParameter input[name="uom_terkoreksi[]"], #tableParameter input[name="formula[]"],#tableParameter input[name="klhk_code[]"] ,#tableParameter input[name="desc[]"]').on('input',function() {
            setChange(this);
        });
        $('#tableParameter input[name="send_klhk[]"], #tableParameter input[name="maintenance[]"],#tableParameter select[name="maintenance_description[]"]').on('change', function(){
            setChange(this)
        })

        $('#tableShutdown').DataTable({
            processing: true,
            serverSide: true,
            destroy: true,
            responsive: true,
            order: [[1, 'desc']],
            columns: [
                { data: 'created_at' },
                { data: 'hour' },
                { data: 'executed' },
                { data: 'timestamp_executed' },
                { data: 'timestamp_end' },
                { data: 'scheduleId' }
            ],
            columnDefs: [
                {
                    targets: [2],
                    render: function(data) {
                        return data == 1 ? "Yes" : "No"
                    }
                },{
                    targets: [-1],
                    render: function(data, type, row) {
                        return row.executed == 1 ? 'Schedule Executed' : `
                            <div class="d-flex flex-row">
                                <button class="btn btn-sm btn-danger m-auto" onclick="cancelSchedule(${data})">
                                    Cancel
                                </button>
                            </div>
                        `
                    }
                },{
                    targets: [3,4], 
                    render: function(data) {
                        return data ?? '-'
                    }
                }
            ],
            ajax: {
                url: "<?= base_url() . '/cems/ajax_list_schedule_shutdown' ?>",
                type: "POST",
                data: {
                    cemsId: parseInt(<?= isset($cems->cemsId) ? $cems->cemsId : "0" ?>)
                },
            }

        });
    })

    function cancelSchedule(id){
        swal.fire({
            icon: 'question',
            title: 'Cancel Schedule ?',
            showCancelButton: true,
            allowOutsideClick: false
        }).then(res => {
            if(res.value){
                swal.fire({
                    icon: 'info',
                    title: 'Please Wait..',
                    showConfirmButton: false,
                    allowOutsideClick: false
                })
                
                let form = new FormData();
                form.append('id', id);
                form.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
                fetch('<?= '/cems/removeSchedule' ?>', {
                    method: 'POST',
                    body: form
                }).then((res) => res.json()).then(res => {
                    if(res.status){
                        fetch('<?= env('NODE_URL') ?>/triggershutdown',{
                            method: 'POST'
                        })
                        swal.fire({
                            icon: 'success',
                            title: 'Success Remove Data',
                            allowOutsideClick: false
                        }).then(res => {
                            location.reload()
                        })
                    }else {
                        swal.fire({
                            icon: 'error',
                            title: res.message,
                            allowOutsideClick: false
                        })
                    }
                }).catch(er => {
                    swal.fire({
                        icon: 'error',
                        title: 'Error Remove Data',
                        allowOutsideClick: false
                    })
                })
            }
        })
    }

    function setChange(evt) {
        var tr = $(evt).closest("tr");
        var input = $(evt).closest("input");
        if (tr.attr("add-update") == "update") {
            var valParam = tr.find('input[name="parameterName[]"]').val();
            var firstValParam = tr.find('input[name="parameterName[]"]').attr("firstValue");
            var valSource = tr.find('input[name="source[]"]').val();
            var firstValSource = tr.find('input[name="source[]"]').attr("firstValue");
            var valHigh = tr.find('input[name="high_terukur[]"]').val();
            var firstValhigh = tr.find('input[name="high_terukur[]"]').attr("firstValue");
            var valHighHigh = tr.find('input[name="highHigh_terukur[]"]').val();
            var firstValHighHigh = tr.find('input[name="highHigh_terukur[]"]').attr("firstValue");
            var valHigh_ = tr.find('input[name="high_terkoreksi[]"]').val();
            var firstValhigh_ = tr.find('input[name="high_terkoreksi[]"]').attr("firstValue");
            var valHighHigh_ = tr.find('input[name="highHigh_terkoreksi[]"]').val();
            var firstValHighHigh_ = tr.find('input[name="highHigh_terkoreksi[]"]').attr("firstValue");
            var valCodeVal = tr.find('input[name="codeVal[]"]').val();
            var firstValCodeVal = tr.find('input[name="codeVal[]"]').attr("firstValue");
            var valUomUkur = tr.find('input[name="uom_terukur[]"]').val();
            var firstValUomUkur = tr.find('input[name="uom_terukur[]"]').attr("firstValue");
            var valUomKoreksi = tr.find('input[name="uom_terkoreksi[]"]').val();
            var firstValUomKoreksi = tr.find('input[name="uom_terkoreksi[]"]').attr("firstValue");
            var valFormula = tr.find('input[name="formula[]"]').val();
            var firstValFormula = tr.find('input[name="formula[]"]').attr("firstValue");
            var valDesc = tr.find('input[name="desc[]"]').val();
            var firstValDesc = tr.find('input[name="desc[]"]').attr("firstValue");
            var valKLHK = tr.find('input[name="send_klhk[]"]').is(':checked') ? 1 : 0;
            var firstValKLHK = tr.find('input[name="send_klhk[]"]').attr("firstValue");
            var valMaintenance = tr.find('input[name="maintenance[]"]').is(':checked') ? 1 : 0;
            var firstValMaintenance = tr.find('input[name="maintenance[]"]').attr("firstValue");
            // var valMaintenance_scheduling = tr.find('input[name="maintenance_scheduling[]"]').is(':checked') ? 1 : 0;
            // var firstValMaintenance_scheduling = tr.find('input[name="maintenance_scheduling[]"]').attr("firstValue");
            var valMaintenance_description = tr.find('select[name="maintenance_description[]"]').val();
            var firstValMaintenance_description = tr.find('select[name="maintenance_description[]"]').attr("firstValue");
            var valKlhkCode = tr.find('input[name="klhk_code[]"]').val();
            var firstValKlhkCode = tr.find('input[name="klhk_code[]"]').attr("firstValue");
            // var valgaugeMin = tr.find('input[name="gauge_min[]"]').val();
            // var firstValgaugeMin = tr.find('input[name="gauge_min[]"]').attr("firstValue");
            // var valgaugeMax = tr.find('input[name="gauge_max[]"]').val();
            // var firstValgaugeMax = tr.find('input[name="gauge_max[]"]').attr("firstValue");

            if (valParam == firstValParam && 
                valSource == firstValSource && 
                valHigh == firstValhigh && 
                valHighHigh == firstValHighHigh &&
                valHigh_ == firstValhigh_ && 
                valHighHigh_ == firstValHighHigh_ &&
                valCodeVal == firstValCodeVal && 
                valUomUkur == firstValUomUkur && 
                valUomKoreksi == firstValUomKoreksi && 
                valDesc == firstValDesc && 
                valFormula == firstValFormula && 
                valKLHK == firstValKLHK && 
                valMaintenance == firstValMaintenance && 
                valKlhkCode == firstValKlhkCode && 
                // valMaintenance_scheduling == firstValMaintenance_scheduling && 
                valMaintenance_description == firstValMaintenance_description
                ) {
                tr.attr("isChange", "0")
                // console.log(0)
            } else {
                tr.attr("isChange", "1")
                // console.log(1)
            }
        }
    }

    function newRowParameter() {
        $("#tableParameter tbody").append(`
            <tr add-update="add" data-id="0">
                <td><input type="text" name="parameter[]" placeholder="Type here" class="input-transparent" value="" /></td>
                <td><input type="text" name="source[]" placeholder="Type here" class="input-transparent" value="" /></td>
                <td><input type="number" name="high_terukur[]" placeholder="Type here" class="input-transparent" value="" /></td>
                <td><input type="number" name="highHigh_terukur[]" placeholder="Type here" class="input-transparent" value="" /></td>
                <td><input type="number" name="high_terkoreksi[]" placeholder="Type here" class="input-transparent" value="" /></td>
                <td><input type="number" name="highHigh_terkoreksi[]" placeholder="Type here" class="input-transparent" value="" /></td>
                <td><input disabled readonly type="number" name="codeVal[]" placeholder="Type here" class="input-transparent" value="${parseInt($("#tableParameter tbody tr:last-child")[0].children[6].children[0].value)+1}" /></td>
                <td><input type="text" name="uom_terukur[]" placeholder="Type here" class="input-transparent" value="" /></td>
                <td><input type="text" name="uom_terkoreksi[]" placeholder="Type here" class="input-transparent" value="" /></td>
                <td><input type="text" name="formula[]" placeholder="Type here" class="input-transparent" value="" /></td>
                <td><input type="text" name="desc[]" placeholder="Type here" class="input-transparent" value="" /></td>
                <td style="max-width: 80px;">
                    <span>auto-active</span>
                </td>
                <td style="min-width: 100px !important;">
                    <div class="ckbx-style-13">
                        <input type="checkbox" id="check_${parseInt($("#tableParameter tbody tr:last-child")[0].children[12].children[0].children[0].id.split('_')[1])+1}" name="send_klhk[]">
                        <label for="check_${parseInt($("#tableParameter tbody tr:last-child")[0].children[12].children[0].children[0].id.split('_')[1])+1}"></label>
                    </div>
                </td>
                <td><input type="text" name="klhk_code[]" placeholder="Type here" class="input-transparent" value="" /></td>
                <td style="min-width: 80px !important;">
                    <div class="ckbx-style-13">
                        <input type="checkbox" id="maintenance_${parseInt($("#tableParameter tbody tr:last-child")[0].children[14].children[0].children[0].id.split('_')[1])+1}" name="maintenance[]">
                        <label for="maintenance_${parseInt($("#tableParameter tbody tr:last-child")[0].children[14].children[0].children[0].id.split('_')[1])+1}"></label>
                    </div>
                </td>
                <td>
                    <div class="form-group mb-0">
                        <select class="form-control" id="maintenance_description_${parseInt($("#tableParameter tbody tr:last-child")[0].children[14].children[0].children[0].id.split('_')[1])+1}" name="maintenance_description[]">
                            <option value="-">Set Description</option>
                            <option value="DAS Fault">DAS Fault</option>
                            <option value="Sensor Fault">Sensor Fault</option>
                            <option value="Connection Fault">Connection Fault</option>
                            <option value="Analyzer Fault">Analyzer Fault</option>
                            <option value="Calibration">Calibration</option>
                        </select>
                    </div>
                </td>
                <td class="text-center">
                    <button  onclick="removeRestoreRowParameter(this)" class="btn btn-sm rounded-pill btn-danger  removeRestoreRow  d-flex flex-row"><i class="fa fa-times my-auto mr-2"></i> <span>delete</span></button>
                </td>
            </tr>
        `);
    }
    
    
    // <td style="min-width: 80px !important;">
    //                 <div class="ckbx-style-13">
    //                     <input type="checkbox" id="maintenance_scheduling${parseInt($("#tableParameter tbody tr:last-child")[0].children[14].children[0].children[0].id.split('_')[1])+1}" name="maintenance_scheduling[]">
    //                     <label for="maintenance_scheduling${parseInt($("#tableParameter tbody tr:last-child")[0].children[14].children[0].children[0].id.split('_')[1])+1}"></label>
    //                 </div>
    //             </td>

    function removeRestoreRowParameter(evt) {
        var tr = $(evt).closest("tr");
        if (tr.attr("add-update") == "add") {
            tr.remove();
        } else {
            if (tr.hasClass("delete")) {
                tr.removeClass("delete");
                if(tr.data('active') == 1){
                    $(evt).html(`<i class="fa fa-times my-auto mr-2"></i> <span>deactivate</span>`);
                }else if(tr.data('active') == 0){
                    $(evt).html(`<i class="fa fa-times my-auto mr-2"></i> <span>activate</span>`);
                }
            } else {
                tr.addClass("delete");
                $(evt).html(`<i title="will delete on save" class="fa fa-undo my-auto mr-2"></i> <span>undo</span>`);
            }
        }
        // $(evt).closest("tr").remove();
    }

    function getDataParameter() {
        var dataTableParameter = [];
        $("#tableParameter tbody tr").each(function() {
            if (($(this).attr("isChange") == 1 && $(this).attr("add-update") == "update") || $(this).attr("add-update") == "add" || $(this).attr("class") == "delete") {

                var parameterName = $(this).find('input[name="parameterName[]"]').val();
                var source = $(this).find('input[name="source[]"]').val();
                var high_terukur = $(this).find('input[name="high_terukur[]"]').val();
                var highHigh_terukur = $(this).find('input[name="highHigh_terukur[]"]').val();
                var high_terkoreksi = $(this).find('input[name="high_terkoreksi[]"]').val();
                var highHigh_terkoreksi = $(this).find('input[name="highHigh_terkoreksi[]"]').val();
                var codeVal = $(this).find('input[name="codeVal[]"]').val();
                var uom_terukur = $(this).find('input[name="uom_terukur[]"]').val();
                var uom_terkoreksi = $(this).find('input[name="uom_terkoreksi[]"]').val();
                var formula = $(this).find('input[name="formula[]"]').val();
                var desc = $(this).find('input[name="desc[]"]').val();
                var send_klhk = $(this).find('input[name="send_klhk[]"]').is(':checked') ? 'on' : 'off';
                var maintenance = $(this).find('input[name="maintenance[]"]').is(':checked') ? 'on' : 'off';
                var klhk_code = $(this).find('input[name="klhk_code[]"]').val();
                // var maintenance_scheduling = $(this).find('input[name="maintenance_scheduling[]"]').is(':checked') ? 'on' : 'off';
                var maintenance_description = $(this).find('select[name="maintenance_description[]"]').val();


                //for removing value we need to know last value
                var parameterName_ = $(this).find('input[name="parameterName[]"]').attr('firstValue')
                var source_ = $(this).find('input[name="source[]"]').attr('firstValue')
                var high_terukur_ = $(this).find('input[name="high_terukur[]"]').attr('firstValue')
                var highHigh_terukur_ = $(this).find('input[name="highHigh_terukur[]"]').attr('firstValue')
                var high_terkoreksi_ = $(this).find('input[name="high_terkoreksi[]"]').attr('firstValue')
                var highHigh_terkoreksi_ = $(this).find('input[name="highHigh_terkoreksi[]"]').attr('firstValue')
                var codeVal_ = $(this).find('input[name="codeVal[]"]').attr('firstValue')
                var uom_terukur_ = $(this).find('input[name="uom_terukur[]"]').attr('firstValue')
                var uom_terkoreksi_ = $(this).find('input[name="uom_terkoreksi[]"]').attr('firstValue')
                var formula_ = $(this).find('input[name="formula[]"]').attr('firstValue')
                var desc_ = $(this).find('input[name="desc[]"]').attr('firstValue')
                var send_klhk_ = $(this).find('input[name="send_klhk[]"]').attr('firstValue')
                var maintenance_ = $(this).find('input[name="maintenance[]"]').attr('firstValue')
                var klhk_code_ = $(this).find('input[name="klhk_code[]"]').attr('firstValue')
                // var maintenance_scheduling_ = $(this).find('input[name="maintenance_scheduling[]"]').attr('firstValue')
                var maintenance_description_ = $(this).find('select[name="maintenance_description[]"]').attr('firstValue')

                // var gauge_min = $(this).find('input[name="gauge_min[]"]').val();
                // var gauge_max = $(this).find('input[name="gauge_max[]"]').val();
                var addUpdate = $(this).attr("add-update");
                var deleting = $(this).attr("class");




                if($(this).attr("add-update") == "update"){
                    if (parameterName != "" || 
                        source != null || 
                        high_terukur != "" || 
                        highHigh_terukur != "" || 
                        high_terkoreksi != "" ||
                        highHigh_terkoreksi != "" ||
                        codeVal != "" ||
                        deleting != "" ||
                        uom_terukur != "" ||
                        uom_terkoreksi != "" ||
                        formula != "" ||
                        send_klhk != "" ||
                        maintenance != "" ||
                        klhk_code != "" ||
                        // maintenance_scheduling != "" ||
                        maintenance_description != "") {
                        dataTableParameter.push({
                            "cemsParameterId": $(this).attr("data-id"),
                            "parameterName": parameterName ? parameterName : (parameterName_ != '' ? '-' : parameterName),
                            "source": source ? source : (source_ != '' ? '-' : source),
                            "high_terukur": high_terukur ? high_terukur : (high_terukur_ != '' ? '-' : high_terukur),
                            "highHigh_terukur": highHigh_terukur ? highHigh_terukur : (highHigh_terukur_ != '' ? '-' : highHigh_terukur),
                            "high_terkoreksi": high_terkoreksi ? high_terkoreksi : (high_terkoreksi_ != '' ? '-' : high_terkoreksi),
                            "highHigh_terkoreksi": highHigh_terkoreksi ? highHigh_terkoreksi : (highHigh_terkoreksi_ != '' ? '-' : highHigh_terkoreksi),
                            "codeVal": codeVal ? codeVal : (codeVal_ != '' ? '-' : codeVal),
                            "uom_terukur": uom_terukur ? uom_terukur : (uom_terukur_ != '' ? '-' : uom_terukur),
                            "uom_terkoreksi": uom_terkoreksi ? uom_terkoreksi : (uom_terkoreksi_ != '' ? '-' : uom_terkoreksi),
                            "formula": formula ? formula : (formula_ != '' ? '-' : formula),
                            "desc": desc ? desc : (desc_ != '' ? '-' : desc),
                            "send_klhk": send_klhk,
                            "klhk_code": klhk_code,
                            "maintenance": maintenance,
                            // "maintenance_scheduling": maintenance_scheduling,
                            "maintenance_description": maintenance_description,
                            "timestamp": moment().format(),
                            "addUpdate": addUpdate,
                            "deleting": deleting ?? "-",
                        });
                    }
                }
                if($(this).attr("add-update") == "add"){
                    if (parameterName != "" && high_terukur != "" && highHigh_terukur != "" && uom_terukur != "") {
                        dataTableParameter.push({
                            "cemsParameterId": $(this).attr("data-id"),
                            "parameterName": parameterName,
                            "source": source,
                            "high_terukur": high_terukur,
                            "highHigh_terukur": highHigh_terukur,
                            "high_terkoreksi": high_terkoreksi,
                            "highHigh_terkoreksi": highHigh_terkoreksi,
                            "codeVal": codeVal,
                            "uom_terukur": uom_terukur,
                            "uom_terkoreksi": uom_terkoreksi,
                            "formula": formula,
                            "send_klhk": send_klhk,
                            "maintenance": maintenance,
                            "klhk_code": klhk_code,
                            // "maintenance_scheduling": maintenance_scheduling,
                            "maintenance_description": maintenance_description,
                            "desc": desc,
                            "timestamp": moment().format(),
                            "addUpdate": addUpdate,
                            "deleting": deleting ?? "-",
                        });
                    }
                }

            }
        });
        return dataTableParameter;
    }

    function openModalCopy(){
        new coreui.Modal(document.getElementById('modalCEMS')).show()
        document.getElementById('cardCems').innerHTML = ''
        document.getElementById('cardCems').insertAdjacentHTML('beforeend', `
            <h5>CEMS List</h5>
        `)
        if(CEMS.length > 0){
            CEMS.map(x => {
                document.getElementById('cardCems').insertAdjacentHTML('beforeend', `
                    <div class="form-check mb-1">
                        <input class="form-check-input" type="radio" name="copyCEMS" onchange="selectCopyCEMS(${x.cemsId})" id="${x.cemsId}" value="${x.cemsId}">
                        <label class="form-check-label" for="${x.cemsId}">
                            ${x.name}
                        </label>
                    </div>
                `);
            })
        }
    }

    function selectCopyCEMS(id){
        let CEMSParameter = JSON.parse('<?php echo json_encode($cemsParameterList); ?>');
        document.getElementById('cardParameter').querySelector('ul').remove()
        document.getElementById('cardParameter').insertAdjacentHTML('beforeend', `
            <ul class="list-group list-group-flush" id="cardParameterList">
            </ul>
        `)
        if(CEMSParameter.length > 0){
            if(CEMSParameter.filter(x => x.cemsId == id).length > 0){
                document.querySelector('#cardParameter').querySelector('#cardParameterList').innerHTML = ''
                CEMSParameter.filter(x => x.cemsId == id).map(x => {
                    document.querySelector('#cardParameter').querySelector('#cardParameterList').insertAdjacentHTML('beforeend', `
                        <li class="list-group-item">
                            ${x.parameterName}
                        </li>
                    `);
                })
            }else {
                document.querySelector('#cardParameter').querySelector('#cardParameterList').insertAdjacentHTML('beforeend', `
                    <li class="list-group-item">
                        Parameter Empty
                    </li>
                `);
            }
        }
    }

    function copyCems(){
        swal.fire({
            icon: 'question',
            title: 'Save Update ?',
            text: 'Developer tidak bertanggung jawab atas input / kesalahan ketik data dalam formulasi pelaporan . dan atas persetujuan bersama bahwa serah terima software adalah menjadi tanggung jawab pengguna . Jika terjadi pelanggaran hukum yang terjadi atas tindakan yg  disengaja ataupun tidak disengaja , sudah bukan lagi tanggung jawab developer',
            showCancelButton: true,
            allowOutsideClick: false
        }).then(res => {
            if(res.value){
                if(document.querySelector('[name=copyCEMS]:checked')?.value){
                    swal.fire({
                        title: 'Please Waiting',
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        icon: 'info'
                    })
                    $.ajax({
                        url: "<?= '/'; ?>cems/copyCEMS",
                        type: "POST",
                        data: {
                            copyCemsId: document.querySelector('[name=copyCEMS]:checked')?.value,
                            cemsId: <?= $cems->cemsId ?>
                        },
                        dataType: 'json',
                        success: function(json) {
                            if (json.status == 200) {
                                fetch('<?= env('NODE_URL') ?>/triggersql',{
                                    method: 'POST'
                                })
                                fetch('<?= env('NODE_URL') ?>/triggershutdown',{
                                    method: 'POST'
                                })
                                swal.close()
                                success();
                                //
                            } else {
                                swal.close()
                                errorsave();
                            }
                        },
                        error: function(errormessage) {
                            error();

                        },
                    });
                }else {
                    swal.fire({
                        icon: 'error',
                        title: 'Cancelled',
                        text: 'Select CEMS',
                        showConfirmButton: false,
                        timer: 1500
                    })
                }
            }
        })
    }

    function setSwitch(index){
        masterParameter.forEach(x => {
            document.querySelector('#tableParameter').querySelector('tbody').querySelectorAll('tr').forEach(y => y.setAttribute('ischange', 1))
            if(updated){
                document.querySelector('#tableParameter').querySelector(`input[value=${x.parameterName}]`).parentElement.parentElement.children[index].children[0].children[0].checked = true
            }else {
                document.querySelector('#tableParameter').querySelector(`input[value=${x.parameterName}]`).parentElement.parentElement.children[index].children[0].children[0].checked = false
            }
        })

        updated = !updated
    }

    function deleteCems(){
        swal.fire({
            icon: 'warning',
            title: 'Delete Data ?',
            text: 'Developer tidak bertanggung jawab atas input / kesalahan ketik data dalam formulasi pelaporan . dan atas persetujuan bersama bahwa serah terima software adalah menjadi tanggung jawab pengguna . Jika terjadi pelanggaran hukum yang terjadi atas tindakan yang  disengaja ataupun tidak disengaja , sudah bukan lagi tanggung jawab developer',
            showCancelButton: true,
            allowOutsideClick: false
        }).then(res => {
            if(res.value){
                swal.fire({
                    title: 'Please Waiting',
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    icon: 'info'
                })
                let id = parseInt(<?= isset($cems->cemsId) ? $cems->cemsId : "0" ?>)
                let formData = new FormData();
                formData.append("<?= csrf_token() ?>", '<?= csrf_hash() ?>')
                formData.append("cemsId", id)
                fetch("<?= base_url('cems/deleteCems') ?>", {
                    method: 'POST',
                    body: formData
                }).then(res => res.json())
                .then(json => {
                    if (json.status) {
                        swal.close()
                        swal.fire({
                            icon: 'success',
                            title: 'Success process data',
                            text: 'Cems data deleted succesfuly'
                        }).then(() => location.href = '<?= base_url() ?>')
                    } else {
                        swal.close()
                        errorsave(json.message);
                    }
                }).catch(er => {
                    error();
                })
            }
        })
    }

    function saveOnlyCems(){
        swal.fire({
            icon: 'question',
            title: 'Save Update ?',
            text: 'Developer tidak bertanggung jawab atas input / kesalahan ketik data dalam formulasi pelaporan . dan atas persetujuan bersama bahwa serah terima software adalah menjadi tanggung jawab pengguna . Jika terjadi pelanggaran hukum yang terjadi atas tindakan yang  disengaja ataupun tidak disengaja , sudah bukan lagi tanggung jawab developer',
            showCancelButton: true,
            allowOutsideClick: false
        }).then((res) => {
            if(res.value) {
                swal.fire({
                    title: 'Please Waiting',
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    icon: 'info'
                })
                let id = parseInt(<?= isset($cems->cemsId) ? $cems->cemsId : "0" ?>)
                var name = $("#formCems input[name='name']").val() ?? null;
                var group = $("#formCems input[name='group']").val() ?? null;
                var latitude = parseFloat($("#formCems input[name='latitude']").val() ?? 0);
                var longitude = parseFloat($("#formCems input[name='longitude']").val() ?? 0);
                var dimension = $("#formCems input[name='dimension']").val() ?? null;
                var fuel = $("#formCems input[name='fuel']").val() ?? null;
                var status = $("#formCems input[name='status']:checked").attr("label") ?? "Running";
                var show_dashboard = document.querySelector("#formCems input[name='show_dashboard']:checked") ? document.querySelector("#formCems input[name='show_dashboard']:checked").value : null
                var use_unit_scheduling = document.querySelector('[name="use_unit_scheduling"]')?.checked ?? null
                var unit_scheduling = document.querySelector('#unit_scheduling').value ?? null
                let formData = new FormData();
                formData.append("<?= csrf_token() ?>", '<?= csrf_hash() ?>')
                formData.append("cemsId", id)
                formData.append("name",name)
                formData.append("group",group)
                formData.append("latitude",latitude)
                formData.append("longitude",longitude)
                formData.append("dimension",dimension)
                formData.append("fuel",fuel)
                formData.append("status",status)
                formData.append("show_dashboard",show_dashboard)
                if(use_unit_scheduling){
                    formData.append("unit_scheduling", unit_scheduling)
                }
                fetch("<?= base_url('cems/saveOnlyCems') ?>", {
                    method: 'POST',
                    body: formData
                }).then(res => res.json())
                .then(json => {
                    if (json.status) {
                        fetch('<?= env('NODE_URL') ?>/triggersql',{
                            method: 'POST'
                        })
                        swal.close()
                        success();
                    } else {
                        swal.close()
                        errorsave(json.message);
                    }
                }).catch(er => {
                    console.log(er)
                    error();
                })
            }
        })
    }

    function saveOnlyParameter() {
        swal.fire({
            icon: 'question',
            title: 'Save Update ?',
            text: 'Developer tidak bertanggung jawab atas input / kesalahan ketik data dalam formulasi pelaporan . dan atas persetujuan bersama bahwa serah terima software adalah menjadi tanggung jawab pengguna . Jika terjadi pelanggaran hukum yang terjadi atas tindakan yg  disengaja ataupun tidak disengaja , sudah bukan lagi tanggung jawab developer',
            showCancelButton: true,
            allowOutsideClick: false
        }).then((res) => {
            if(res.value) {
                swal.fire({
                    title: 'Please Waiting',
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    icon: 'info'
                })
                let id = parseInt(<?= isset($cems->cemsId) ? $cems->cemsId : "0" ?>)
                let formData = {}
                formData['<?= csrf_token() ?>'] = '<?= csrf_hash() ?>'
                formData["cemsId"]= id
                formData["tblm_cemsParameter"] = getDataParameter() ?? []
                $.ajax({
                    url: "<?= '/'; ?>cems/saveOnlyParameter",
                    type: "POST",
                    data: formData,
                    dataType: 'json',
                    success: function(json) {
                        if (json.status == 200) {
                            fetch('<?= env('NODE_URL') ?>/triggersql',{
                                method: 'POST'
                            })
                            fetch('<?= env('NODE_URL') ?>/triggershutdown',{
                                method: 'POST'
                            })
                            swal.close()
                            success();
                            //
                        } else {
                            swal.close()
                            errorsave();
                        }
                    },
                    error: function(errormessage) {
                        console.log(errormessage)
                        error();

                    },
                });
            }
        })
    }

    function error() {

        Swal.fire({
            title: 'Error update data! ',
            icon: 'error',
            confirmButtonText: 'oke'
        })

    }

    function success() {

        Swal.fire({
                title: 'Succes update!',
                icon: 'success',
                confirmButtonText: 'oke'

            })
            .then(function(isConfirm) {
                if (isConfirm)
                    window.location.reload();
            })
    }

    function errorsave(msg) {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: msg ?? 'Error update data',
            confirmButtonText: 'oke'
        })


    }
</script>



<?= $this->endSection() ?>