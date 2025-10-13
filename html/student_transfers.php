<?php
$page_title = 'Student Transfers';
$page_description = 'Information and procedures for student transfers at Zanzibar University';
$page_heading = 'Student Transfers';

// Check if required files exist
$header_file = 'includes/about_header.php';
$footer_file = 'includes/about_footer.php';

if (!file_exists($header_file) || !file_exists($footer_file)) {
    header('Location: not_found.php');
    exit();
}

include_once $header_file;
?>

<!-- Page Content -->
<div style="background-color: #f8f9fa; min-height: 100vh; padding: 40px 0;">
    <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 15px;">
        <div class="row" style="margin: 0 -15px;">
        <div class="col-12" style="padding: 0 15px; margin-bottom: 30px;">
            <h1 style="color: #004225; font-size: 2.2rem; text-align: center; margin-bottom: 2rem; padding-bottom: 1rem; border-bottom: 2px solid #e9ecef; font-weight: 600;">
                Transfer of Credit Units from Other Recognized Institutions
            </h1>
            
            <div style="background: #fff; border-radius: 8px; box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08); margin-bottom: 30px; border-left: 4px solid #004225; transition: all 0.3s ease;">
                <div style="padding: 25px;">
                    <h2 style="color: #004225; font-size: 1.5rem; margin: 0 0 1.2rem 0; padding-bottom: 0.8rem; border-bottom: 2px solid #f0f0f0; font-weight: 600;">Postgraduate Students</h2>
                    <p style="color: #333; font-size: 1.05rem; line-height: 1.8; margin-bottom: 1.2rem;">Postgraduate candidates from other recognized Institutions may transfer their credits to ZU.</p>
                    <p style="color: #333; font-size: 1.05rem; line-height: 1.8; margin-bottom: 1.2rem;">Candidates from other recognized Institutions who would like to complete their postgraduate programmes at the Zanzibar University, may apply to Senate using Postgraduate form number ZU/PG.F20 through respective Faculty/Institutes and Senate Postgraduate Studies Committee to transfer from their previous Institution credits/units at least 50% of the total credits/units for the programme, provided the candidates meet the minimum entry qualifications for the programme in which they wish to enrol. For purposes of this regulation, the term 'entry qualification' shall include the respective programmes cut-off point in the relevant year.</p>
                    <ul style="padding-left: 1.8rem; margin-bottom: 1.5rem;">
                        <li style="margin-bottom: 0.8rem; line-height: 1.7; color: #333; position: relative;">Credit transfer can only be allowed if such credits have been obtained within a period of not more than two years.</li>
                        <li style="margin-bottom: 0.8rem; line-height: 1.7; color: #333; position: relative;">Students discontinued from other Institutions shall not be allowed to transfer credits to the Zanzibar University.</li>
                        <li style="margin-bottom: 0.8rem; line-height: 1.7; color: #333; position: relative;">Students shall be required to undertake at least 50% of degree programme credit units at ZU. Maximum credit allowable for transfer, therefore, is 50% of the required credit units of a ZU degree programme.</li>
                        <li style="margin-bottom: 0.8rem; line-height: 1.7; color: #333; position: relative;">ZU students on study-abroad programmes shall be allowed to transfer credits obtained from the other Institutions to ZU.</li>
                        <li style="margin-bottom: 0.8rem; line-height: 1.7; color: #333; position: relative;">Transfer of credits from ZU to other Institutions shall be governed by regulations of the receiving Institutions.</li>
                    </ul>
                </div>
            </div>

            <div style="background: #fff; border-radius: 8px; box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08); margin-bottom: 30px; border-left: 4px solid #004225; transition: all 0.3s ease;">
                <div style="padding: 25px;">
                    <h2 style="color: #004225; font-size: 1.5rem; margin: 0 0 1.2rem 0; padding-bottom: 0.8rem; border-bottom: 2px solid #f0f0f0; font-weight: 600;">Undergraduate Student Credit Transfer</h2>
                    <p style="color: #333; font-size: 1.05rem; line-height: 1.8; margin-bottom: 1.2rem;">Undergraduate Student credit transfer is allowed only among Universities which are fully accredited by a recognized body in the country. A student may transfer credit units from another institution upon satisfying the following:</p>
                    <ul style="padding-left: 1.8rem; margin-bottom: 1.5rem;">
                        <li style="margin-bottom: 0.8rem; line-height: 1.7; color: #333; position: relative;">The admission requirements for the academic programme applied for.</li>
                        <li style="margin-bottom: 0.8rem; line-height: 1.7; color: #333; position: relative;">Courses for transfer must have been accredited by the Commission and/or another National Accreditation Board.</li>
                        <li style="margin-bottom: 0.8rem; line-height: 1.7; color: #333; position: relative;">Transfer of equivalency of subjects, modules, courses and credit transfer is subject to the approval of TCU/NACTE through relevant Department, Faculty and DVC (Academic). Students from Foreign Universities shall be allowed on submission of Certificate of No Objection, Admission Letter and confirmation from recognized releasing institution.</li>
                        <li style="margin-bottom: 0.8rem; line-height: 1.7; color: #333; position: relative;">The subject, course or module intended for credit accumulation must be relevant to the programme to which the student is to be registered.</li>
                        <li style="margin-bottom: 0.8rem; line-height: 1.7; color: #333; position: relative;">Submission of an official statement of results from the releasing institution.</li>
                        <li style="margin-bottom: 0.8rem; line-height: 1.7; color: #333; position: relative;">Obtaining CGPA of at least 2.0 in a scale of 5.0 depending on the year of transfer.</li>
                        <li style="margin-bottom: 0.8rem; line-height: 1.7; color: #333; position: relative;">A student who intends to transfer his/her credits for purposes of graduating at Zanzibar University, shall be required to earn at least 50% of the total credits in the core course of the particular programme of the releasing institution.</li>
                        <li style="margin-bottom: 0.8rem; line-height: 1.7; color: #333; position: relative;">Transfer of credits shall be allowable within a period not exceeding five years from the time were earned.</li>
                        <li style="margin-bottom: 0.8rem; line-height: 1.7; color: #333; position: relative;">The transferring student shall have cleared all his/her supplementary examinations at the releasing institution but may be allowed to transfer carry overs to Zanzibar University.</li>
                        <li style="margin-bottom: 0.8rem; line-height: 1.7; color: #333; position: relative;">The transferring student shall understand and accept the terms and conditions regarding the sought programme at Zanzibar University.</li>
                        <li style="margin-bottom: 0.8rem; line-height: 1.7; color: #333; position: relative;">The transferring student shall have cleared all missed courses before Graduation.</li>
                    </ul>
                </div>
            </div>

            <div style="background: #fff; border-radius: 8px; box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08); margin-bottom: 30px; border-left: 4px solid #004225; transition: all 0.3s ease;">
                <div style="padding: 25px;">
                    <h2 style="color: #004225; font-size: 1.5rem; margin: 0 0 1.2rem 0; padding-bottom: 0.8rem; border-bottom: 2px solid #f0f0f0; font-weight: 600;">Non-degree Programme Transfer</h2>
                    <p style="color: #333; font-size: 1.05rem; line-height: 1.8; margin-bottom: 1.2rem;">Non-Degree Student credit transfer is allowed only between Institutions which are fully accredited by a recognized body in the country. A student may transfer credit units from another institution upon satisfying the following:</p>
                    <ul style="padding-left: 1.8rem; margin-bottom: 1.5rem;">
                        <li style="margin-bottom: 0.8rem; line-height: 1.7; color: #333; position: relative;">The admission requirements for the academic programme applied for.</li>
                        <li style="margin-bottom: 0.8rem; line-height: 1.7; color: #333; position: relative;">Courses for transfer must have been accredited by the Commission and/or another National Accreditation Board.</li>
                        <li style="margin-bottom: 0.8rem; line-height: 1.7; color: #333; position: relative;">Transfer of equivalency of subjects, modules, courses and credit transfer is subject to the approval of TCU through ICE, Department, Faculty and DVC (Academic). Students from Foreign Universities shall be allowed on submission of Certificate of No Objection, Admission Letter and confirmation from recognized releasing institution.</li>
                        <li style="margin-bottom: 0.8rem; line-height: 1.7; color: #333; position: relative;">The subject, course or module intended for credit accumulation must be relevant to the programme to which the student is to be registered.</li>
                        <li style="margin-bottom: 0.8rem; line-height: 1.7; color: #333; position: relative;">Submission of an official statement of results from the releasing institution.</li>
                        <li style="margin-bottom: 0.8rem; line-height: 1.7; color: #333; position: relative;">Obtaining CGPA of at least 2.0 in a scale of 5.0 depending on the year of transfer.</li>
                        <li style="margin-bottom: 0.8rem; line-height: 1.7; color: #333; position: relative;">A student who intends to transfer his/her credits for purposes of graduating at Zanzibar University, shall be required to earn at least 50% of the total credits in the core course of the particular programme of the releasing institution.</li>
                        <li style="margin-bottom: 0.8rem; line-height: 1.7; color: #333; position: relative;">Transfer of credits shall be allowable within a period not exceeding five years from the time were earned.</li>
                        <li style="margin-bottom: 0.8rem; line-height: 1.7; color: #333; position: relative;">The transferring student shall have cleared all his/her supplementary examinations at the releasing institution but may be allowed to transfer carry overs to Zanzibar University.</li>
                        <li style="margin-bottom: 0.8rem; line-height: 1.7; color: #333; position: relative;">The transferring student shall understand and accept the terms and conditions regarding the sought programme at Zanzibar University.</li>
                        <li style="margin-bottom: 0.8rem; line-height: 1.7; color: #333; position: relative;">The transferring student shall have cleared all missed courses before Graduation.</li>
                    </ul>
                    <p style="color: #333; font-size: 1.05rem; line-height: 1.8; margin-bottom: 1.2rem;">For NTA Programmes students shall follow NACTE guideline as prescribed in the website (www.nacte.go.tz).</p>
                </div>
            </div>

            <div style="background: #fff; border-radius: 8px; box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08); margin-bottom: 30px; border-left: 4px solid #004225; transition: all 0.3s ease;">
                <div style="padding: 25px;">
                    <h2 style="color: #004225; font-size: 1.5rem; margin: 0 0 1.2rem 0; padding-bottom: 0.8rem; border-bottom: 2px solid #f0f0f0; font-weight: 600;">Criteria for Establishing Equivalency of Courses</h2>
                    <p style="color: #333; font-size: 1.05rem; line-height: 1.8; margin-bottom: 1.2rem;">In determining the equivalence of courses for purposes of transfer of credits the following criteria shall be used:</p>
                    <ul style="padding-left: 1.8rem; margin-bottom: 1.5rem;">
                        <li style="margin-bottom: 0.8rem; line-height: 1.7; color: #333; position: relative;">The course shall be from a programme of the same level as that of ZU course.</li>
                        <li style="margin-bottom: 0.8rem; line-height: 1.7; color: #333; position: relative;">The course shall have a theoretical component i.e. involving final examination, excluding clinical-based courses.</li>
                        <li style="margin-bottom: 0.8rem; line-height: 1.7; color: #333; position: relative;">Course content shall be at least 60 percent similar to that of the ZU course.</li>
                        <li style="margin-bottom: 0.8rem; line-height: 1.7; color: #333; position: relative;">The number of teaching hours used to cover the course shall not be less than 60 percent of the hours used in the similar course at ZU.</li>
                        <li style="margin-bottom: 0.8rem; line-height: 1.7; color: #333; position: relative;">Once a course has been accepted as being equivalent to a ZU course as per the criteria in this Regulation, the course shall be given the same number of credits as that of the course at ZU regardless of the credits in other recognized Institutions.</li>
                    </ul>
                </div>
            </div>

            <div style="background: #fff; border-radius: 8px; box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08); margin-bottom: 30px; border-left: 4px solid #004225; transition: all 0.3s ease;">
                <div style="padding: 25px;">
                    <h2 style="color: #004225; font-size: 1.5rem; margin: 0 0 1.2rem 0; padding-bottom: 0.8rem; border-bottom: 2px solid #f0f0f0; font-weight: 600;">Grade Conversion</h2>
                    <ul style="padding-left: 1.8rem; margin-bottom: 1.5rem;">
                        <li style="margin-bottom: 0.8rem; line-height: 1.7; color: #333; position: relative;">Conversion of grades shall be done by anchoring the pass mark of the other recognized Institutions to that of ZU and accordingly determining the range of marks in the other University for the ZU grades.</li>
                        <li style="margin-bottom: 0.8rem; line-height: 1.7; color: #333; position: relative;">In the case where only grades (and not scored marks) are available, the lower equivalent grade shall be assumed.</li>
                    </ul>
                </div>
            </div>

            <div style="background: #fff; border-radius: 8px; box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08); margin-bottom: 30px; border-left: 4px solid #004225; transition: all 0.3s ease;">
                <div style="padding: 25px;">
                    <h2 style="color: #004225; font-size: 1.5rem; margin: 0 0 1.2rem 0; padding-bottom: 0.8rem; border-bottom: 2px solid #f0f0f0; font-weight: 600;">Procedures and Administration of Student Credit Transfer</h2>
                    <ul style="padding-left: 1.8rem; margin-bottom: 1.5rem;">
                        <li style="margin-bottom: 0.8rem; line-height: 1.7; color: #333; position: relative;">An application for transfer of credits from other universities to ZU shall be made prior to the commencement of the semester for which the transfer is expected to become effective.</li>
                        <li style="margin-bottom: 0.8rem; line-height: 1.7; color: #333; position: relative;">An Application for credit transfer shall be submitted in writing to TCU/NACTE through relevant Department, Faculty and DVC (Academic) and shall be accompanied by copies of all required supporting documents.</li>
                        <li style="margin-bottom: 0.8rem; line-height: 1.7; color: #333; position: relative;">Supporting documents for credit transfer application shall include the following: Official progressive report, admission letter from the releasing Institutions, course description, catalogue or syllabus (to include number of hours of teaching, method of assessment and grading system), An official translation of the original documents (in case of non-English documents), Photo-attached personal identification documents e.g. Birth certificate, passport or ID, and Certified copies of the original certificates used to gain admission into the releasing Institutions and Certificate of No Objection in case of foreign students.</li>
                        <li style="margin-bottom: 0.8rem; line-height: 1.7; color: #333; position: relative;">The applicant for credit transfer shall pay a non-refundable administration fee to be determined by Senate from time to time. The payment of fee shall not apply to ZU students on study arrangements abroad.</li>
                    </ul>
                </div>
            </div>

            <div style="background: #fff; border-radius: 8px; box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08); margin-bottom: 30px; border-left: 4px solid #004225; transition: all 0.3s ease;">
                <div style="padding: 25px;">
                    <h2 style="color: #004225; font-size: 1.5rem; margin: 0 0 1.2rem 0; padding-bottom: 0.8rem; border-bottom: 2px solid #f0f0f0; font-weight: 600;">Conformity to the University Regulations</h2>
                    <p style="color: #333; font-size: 1.05rem; line-height: 1.8; margin-bottom: 1.2rem;">All registered students are required to conform entirely to the University Charter, as well as Rules and Regulations, which may be issued, from time to time, by the University Council and Senate.</p>
                </div>
            </div>

            <div style="background: #fff; border-radius: 8px; box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08); margin-bottom: 30px; border-left: 4px solid #004225; transition: all 0.3s ease;">
                <div style="padding: 25px;">
                    <h2 style="color: #004225; font-size: 1.5rem; margin: 0 0 1.2rem 0; padding-bottom: 0.8rem; border-bottom: 2px solid #f0f0f0; font-weight: 600;">Change of Programme</h2>
                    <p style="color: #333; font-size: 1.05rem; line-height: 1.8; margin-bottom: 1.2rem;">In exceptional circumstances, new students may be allowed to change programme within the first two weeks upon approval of TCU through Admissions and DVC-Academic Office.</p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php 
if (file_exists('includes/about_footer.php')) {
    include_once 'includes/about_footer.php'; 
} else {
    header('Location: not_found.php');
    exit();
}
?>
</div>



