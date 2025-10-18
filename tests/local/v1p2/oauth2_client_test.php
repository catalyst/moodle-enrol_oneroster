<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * One Roster Enrolment Client Unit tests.
 *
 * @package    enrol_oneroster
 * @copyright  QUT Capstone Team - Abhinav Gandham, Harrison Dyba, Jonathon Foo, Kushi Patel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_oneroster\tests\local\v1p2;

require_once(__DIR__ . '/../oneroster_testcase.php');
use enrol_oneroster\tests\local\oneroster_testcase;
use enrol_oneroster\local\v1p2\oauth2_client;
use enrol_oneroster\local\interfaces\container;

/**
 * One Roster tests for OAuth2 Client.
 *
 * @package    enrol_oneroster
 * @copyright  QUT Capstone Team - Abhinav Gandham, Harrison Dyba, Jonathon Foo, Kushi Patel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @covers  \enrol_oneroster\local\oauth2_client
 */
class oauth2_client_test extends oneroster_testcase {

        /**
     * Get a mock of the abstract container.
     *
     * @return  container
     */
    //need to overide test
    public function test_auth_url_is_unused(): void {
        $client = new \enrol_oneroster\local\v1p2\oauth2_client(
            'https://example.org/token',
            'https://example.org',
            'clientid',
            'clientsecret'
        );

        $rc = new \ReflectionClass(\enrol_oneroster\local\v1p2\oauth2_client::class);
        $rcm = $rc->getMethod('auth_url');
        $rcm->setAccessible(true);

        $this->expectException(\coding_exception::class);
        $rcm->invoke($client);
    }

        /**
     * Test the `authenticate` method.
     */
    public function test_authenticate(): void {
        $tokenurl = 'https://example.com/token';
        $server = 'https://example.com/';
        $clientid = 'thisIsMyClientId';
        $clientsecret = 'thisIsMyBiggestSecret';

        $client = $this->getMockBuilder(oauth2_client::class)
            ->setConstructorArgs([
                $tokenurl,
                $server,
                $clientid,
                $clientsecret
            ])
            ->onlyMethods([
                'authenticate',
                'get_all_scopes',
                'post',
                'get_request_info',
                'get_base_url',
            ])->getMock();

        $scopes = [
            'https://example.org/spec/example/v1p1/scope/example.dosoemthing',
        ];
        $client
            ->method('get_all_scopes')
            ->willReturn($scopes);

        $client
            ->method('post')
            ->willReturn(json_encode((object) [
                'access_token' => 'exampleToken',
                'expires_in' => usergetmidnight(time()) + DAYSECS,
                'scope' => implode(',', $scopes),
            ]));

        $client
            ->method('get_request_info')
            ->willReturn([
                'http_code' => 200,
            ]);

        // Call Authenticate to authenticate the user.
        $client->authenticate();
    }

}
