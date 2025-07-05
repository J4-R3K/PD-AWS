<div class="generator-load">
    <h3>Generator Load Calculator</h3>

    <!-- We wrap the form table in a scrollable container -->
    <div class="form-scroll-container">
        <form id="load-form">
            <table id="equipment-table" class="calc-table">
                <colgroup>
                    <col style="width: 40%;">
                    <col style="width: 13%;">
                    <col style="width: 10%;">
                    <col style="width: 10%;">
                    <col style="width: 10%;">
                    <col style="width: 10%;">
                    <col style="width: 7%;">
                </colgroup>
                <thead>
                    <tr>
                        <th>Equipment</th>
                        <th>
                            Load Type
                            <!-- The tooltip “?” -->
                            <span class="tooltip-wrap"
                                  data-tooltip="DOL – Direct OnLine
SD – Star Delta
SS – Soft Starter
VSD – Variable Speed Drive
STD – Standard (non-motor)">
                                ?
                            </span>
                        </th>
                        <th>Locked Rotor Amps (LRA)</th>
                        <th>Start Current (SC)</th>
                        <th>FLC</th>
                        <th>Start Time (s)</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- One default row -->
                    <tr>
                        <td><input type="text" name="details[]" placeholder="Equipment name"></td>
                        <td>
                            <select name="load_type[]">
                                <option value="DOL">DOL</option>
                                <option value="SD">SD</option>
                                <option value="SS">SS</option>
                                <option value="VSD">VSD</option>
                                <option value="STD">STD</option>
                            </select>
                        </td>
                        <td><input type="text" name="lra[]" placeholder="0"></td>
                        <td><input type="text" name="sc[]" placeholder="0"></td>
                        <td><input type="text" name="flc[]" placeholder="0"></td>
                        <td><input type="text" name="start_time[]" placeholder="1"></td>
                        <td><button type="button" class="remove">X</button></td>
                    </tr>
                </tbody>
            </table>
            <br>
            <button type="button" id="add-equipment">Add Equipment</button>
            <button type="submit" id="calc-button">Calculate</button>
        </form>
    </div>
    
    <div id="results-container" style="overflow-x: auto; max-width: 100%; border: 1px solid #ddd; margin-top: 20px;">
        <div id="results"></div>
    </div>

    <canvas id="load-chart" style="margin-top: 20px;"></canvas>
    <hr>

    <h4>Generate PDF Report</h4>
    <form id="pdf-form">
        <label>Project:</label>
        <input type="text" name="pdf_project"><br>

        <label>Date:</label>
        <input type="date" name="pdf_date"><br>

        <label>Revision:</label>
        <input type="text" name="pdf_revision"><br>

        <label>Generator Description:</label>
        <input type="text" name="pdf_gen_desc"><br>

        <button type="submit">Export as PDF</button>
    </form>
</div>
