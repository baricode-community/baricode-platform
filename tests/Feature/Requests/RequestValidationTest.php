<?php

use App\Http\Requests\CourseStartRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;

uses(RefreshDatabase::class);

describe('Request Validation Tests', function () {
    describe('CourseStartRequest', function () {
        it('authorizes all requests', function () {
            $request = new CourseStartRequest();
            
            expect($request->authorize())->toBeTrue();
        });

        it('validates correct time format for sessions', function () {
            $validData = [
                'days' => [
                    '1' => [
                        'sesi_1' => '09:00',
                        'sesi_2' => '13:00',
                        'sesi_3' => '17:00',
                    ],
                    '2' => [
                        'sesi_1' => '10:00',
                        'sesi_2' => '14:00',
                        'sesi_3' => '18:00',
                    ],
                ]
            ];

            $request = new CourseStartRequest();
            $validator = Validator::make($validData, $request->rules());

            expect($validator->passes())->toBeTrue();
        });

        it('fails validation for invalid time format', function () {
            $invalidData = [
                'days' => [
                    '1' => [
                        'sesi_1' => 'invalid-time',
                        'sesi_2' => '25:00', // Invalid hour
                        'sesi_3' => '12:70', // Invalid minute
                    ]
                ]
            ];

            $request = new CourseStartRequest();
            $validator = Validator::make($invalidData, $request->rules());

            expect($validator->fails())->toBeTrue();
            expect($validator->errors()->has('days.1.sesi_1'))->toBeTrue();
            expect($validator->errors()->has('days.1.sesi_2'))->toBeTrue();
            expect($validator->errors()->has('days.1.sesi_3'))->toBeTrue();
        });

        it('requires days array to be present', function () {
            $invalidData = [];

            $request = new CourseStartRequest();
            $validator = Validator::make($invalidData, $request->rules());

            expect($validator->fails())->toBeTrue();
            expect($validator->errors()->has('days'))->toBeTrue();
        });

        it('validates that days is an array', function () {
            $invalidData = [
                'days' => 'not-an-array'
            ];

            $request = new CourseStartRequest();
            $validator = Validator::make($invalidData, $request->rules());

            expect($validator->fails())->toBeTrue();
            expect($validator->errors()->has('days'))->toBeTrue();
        });

        it('allows nullable session times', function () {
            $dataWithNullableSessions = [
                'days' => [
                    '1' => [
                        'sesi_1' => '09:00',
                        'sesi_2' => null,
                        'sesi_3' => '17:00',
                    ],
                    '2' => [
                        'sesi_1' => null,
                        'sesi_2' => null,
                        'sesi_3' => null,
                    ],
                ]
            ];

            $request = new CourseStartRequest();
            $validator = Validator::make($dataWithNullableSessions, $request->rules());

            expect($validator->passes())->toBeTrue();
        });

        it('allows empty day entries', function () {
            $dataWithEmptyDays = [
                'days' => [
                    '1' => [
                        'sesi_1' => '09:00',
                        'sesi_2' => '13:00',
                        'sesi_3' => '17:00',
                    ],
                    '2' => [],
                    '3' => [
                        'sesi_1' => '10:00',
                    ],
                ]
            ];

            $request = new CourseStartRequest();
            $validator = Validator::make($dataWithEmptyDays, $request->rules());

            expect($validator->passes())->toBeTrue();
        });

        it('validates edge case time values', function () {
            $edgeCaseData = [
                'days' => [
                    '1' => [
                        'sesi_1' => '00:00', // Midnight
                        'sesi_2' => '23:59', // End of day
                        'sesi_3' => '12:30', // Noon with minutes
                    ]
                ]
            ];

            $request = new CourseStartRequest();
            $validator = Validator::make($edgeCaseData, $request->rules());

            expect($validator->passes())->toBeTrue();
        });

        it('fails for malformed time values', function () {
            $malformedData = [
                'days' => [
                    '1' => [
                        'sesi_1' => '9:0',     // Missing leading zero
                        'sesi_2' => '13:5',    // Missing leading zero in minutes
                        'sesi_3' => '1:30',    // Missing leading zero in hour
                    ]
                ]
            ];

            $request = new CourseStartRequest();
            $validator = Validator::make($malformedData, $request->rules());

            expect($validator->fails())->toBeTrue();
            expect($validator->errors()->has('days.1.sesi_1'))->toBeTrue();
            expect($validator->errors()->has('days.1.sesi_2'))->toBeTrue();
            expect($validator->errors()->has('days.1.sesi_3'))->toBeTrue();
        });
    });

    describe('General Request Validation', function () {
        it('handles complex nested validation structures', function () {
            $complexData = [
                'days' => [
                    'monday' => [
                        'sesi_1' => '08:00',
                        'sesi_2' => '12:00',
                        'sesi_3' => '16:00',
                    ],
                    'tuesday' => [
                        'sesi_1' => '09:30',
                        'sesi_2' => '13:30',
                        'sesi_3' => '17:30',
                    ],
                    'wednesday' => [
                        'sesi_1' => '07:45',
                        'sesi_2' => '11:45',
                        'sesi_3' => '15:45',
                    ],
                ]
            ];

            $request = new CourseStartRequest();
            $validator = Validator::make($complexData, $request->rules());

            expect($validator->passes())->toBeTrue();
        });

        it('validates multiple error conditions at once', function () {
            $multipleErrorsData = [
                'days' => [
                    '1' => [
                        'sesi_1' => 'invalid',
                        'sesi_2' => '25:90',
                        'sesi_3' => 'also-invalid',
                    ],
                    '2' => [
                        'sesi_1' => '99:99',
                        'sesi_2' => 'not-time',
                        'sesi_3' => '30:100',
                    ]
                ]
            ];

            $request = new CourseStartRequest();
            $validator = Validator::make($multipleErrorsData, $request->rules());

            expect($validator->fails())->toBeTrue();
            
            $errors = $validator->errors();
            expect($errors->has('days.1.sesi_1'))->toBeTrue();
            expect($errors->has('days.1.sesi_2'))->toBeTrue();
            expect($errors->has('days.1.sesi_3'))->toBeTrue();
            expect($errors->has('days.2.sesi_1'))->toBeTrue();
            expect($errors->has('days.2.sesi_2'))->toBeTrue();
            expect($errors->has('days.2.sesi_3'))->toBeTrue();
        });
    });
});
