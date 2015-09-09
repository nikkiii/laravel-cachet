<?php namespace Nikkiii\Cachet;

/*
 * This file is part of Laravel Cachet.
 *
 * (c) Nikki <nospam@nikkii.us>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Nikkiii\Cachet\Exceptions\CachetAuthenticationException;
use Nikkiii\Cachet\Exceptions\CachetException;
use Psr\Http\Message\ResponseInterface;

/**
 * The CachetConnection class containing our Guzzle client implementation.
 *
 * @author Nikki <nospam@nikkii.us>
 */
class CachetConnection {

	/**
	 * A constant defining the url for Components.
	 */
	const COMPONENT = 'components';

	/**
	 * A constant defining the url for Incidents.
	 */
	const INCIDENT = 'incidents';

	/**
	 * Our Guzzle Client Instance, preconfigured for our connection.
	 *
	 * @var Client
	 */
	private $client;

	public function __construct(array $config) {
		$this->client = new Client([
			'base_uri' => rtrim($config['base_uri'], '/') . '/',
			'headers' => [
				'X-Cachet-Token' => $config['token']
			]
		]);
	}

	/**
	 * Ping our cachet installation.
	 *
	 * @return bool True, if we get a response.
	 * @throws CachetException If the status code returned wasn't what we expected.
	 */
	public function ping() {
		$res = $this->client->get('ping');

		if ($res->getStatusCode() !== 200) {
			throw new CachetException('Invalid response code!');
		}

		$json = $this->json($res);

		return object_get($json, 'data', '') == 'Pong!';
	}

	/**
	 * Gets a list of components.
	 *
	 * @return array
	 * @throws CachetException If the status code returned wasn't what we expected.
	 */
	public function components() {
		return $this->resources(self::COMPONENT);
	}

	/**
	 * Get a specific component.
	 *
	 * @param $id
	 * @return \stdClass
	 * @throws CachetException If the status code returned wasn't what we expected.
	 */
	public function component($id) {
		return $this->get(self::COMPONENT, $id);
	}

	/**
	 * Create a new component.
	 *
	 * @param $name
	 * @param $status
	 * @param string $description
	 * @param string $link
	 * @param int $order
	 * @param int $group_id
	 * @return \stdClass
	 * @throws CachetException If the status code returned wasn't what we expected.
	 */
	public function createComponent($name, $status, $description = '', $link = '', $order = 0, $group_id = 0) {
		return $this->create(self::COMPONENT, compact('name', 'status', 'description', 'link', 'order', 'group_id'));
	}

	/**
	 * Update a component.
	 *
	 * @param $id
	 * @param array $update
	 * @return \stdClass
	 * @throws CachetException If the status code returned wasn't what we expected.
	 * @see https://docs.cachethq.io/docs/update-a-component
	 */
	public function updateComponent($id, array $update) {
		return $this->update(self::COMPONENT, $id, $update);
	}

	/**
	 * Delete a component.
	 *
	 * @param $id
	 * @return bool
	 * @throws CachetException If the status code returned wasn't what we expected.
	 */
	public function deleteComponent($id) {
		return $this->delete(self::COMPONENT, $id);
	}

	/**
	 * Gets a list of incidents.
	 *
	 * @return array
	 * @throws CachetException If the status code returned wasn't what we expected.
	 */
	public function incidents() {
		return $this->resources(self::INCIDENT);
	}

	/**
	 * Get a specific incident.
	 *
	 * @param $id
	 * @return \stdClass
	 * @throws CachetException If the status code returned wasn't what we expected.
	 */
	public function incident($id) {
		return $this->get(self::INCIDENT, $id);
	}

	/**
	 * Create an incident.
	 *
	 * @param $name
	 * @param $message
	 * @param $status
	 * @param bool $visible
	 * @param int $component_id
	 * @param bool $notify
	 * @return \stdClass
	 * @throws CachetException If the status code returned wasn't what we expected.
	 */
	public function createIncident($name, $message, $status, $visible = true, $component_id = 0, $notify = false) {
		return $this->create(self::COMPONENT, compact('name', 'message', 'status', 'visible', 'component_id', 'notify'));
	}

	/**
	 * Update an incident.
	 *
	 * @param $id
	 * @param array $update
	 * @return \stdClass
	 * @throws CachetException If the status code returned wasn't what we expected.
	 * @see https://docs.cachethq.io/docs/update-an-incident
	 */
	public function updateIncident($id, array $update) {
		return $this->update(self::INCIDENT, $id, $update);
	}

	/**
	 * Delete an incident.
	 *
	 * @param $id
	 * @return bool
	 * @throws CachetException If the status code returned wasn't what we expected.
	 */
	public function deleteIncident($id) {
		return $this->delete(self::INCIDENT, $id);
	}

	/**
	 * List resources at the given type url.
	 *
	 * @param $type
	 * @return array
	 * @throws CachetException If the status code returned wasn't what we expected.
	 */
	public function resources($type) {
		$res = $this->client->get($type);

		if ($res->getStatusCode() !== 200) {
			throw new CachetException('Invalid response code!');
		}

		$json = $this->json($res);

		// TODO pagination, possibly collections?

		return $json->data;
	}

	/**
	 * Get a specific resource at the given url with the specified id.
	 *
	 * @param $type
	 * @param $id
	 * @return \stdClass
	 * @throws CachetException If the status code returned wasn't what we expected.
	 */
	public function get($type, $id) {
		try {
			$res = $this->client->get($type . '/' . $id);

			if ($res->getStatusCode() !== 200) {
				throw new CachetException('Invalid response code!');
			}

			$json = $this->json($res);

			return $json->data;
		} catch (RequestException $e) {
			$this->handleException($e);
		}
	}

	/**
	 * Create a resource at the given url.
	 *
	 * @param $type
	 * @param array $data
	 * @return \stdClass
	 * @throws CachetException If the status code returned wasn't what we expected.
	 */
	public function create($type, array $data = []) {
		try {
			$res = $this->client->post($type, [
				'json' => $data
			]);

			if ($res->getStatusCode() !== 200) {
				throw new CachetException('Invalid response code!');
			}

			$json = $this->json($res);

			return $json->data;
		} catch (RequestException $e) {
			$this->handleException($e);
		}
	}

	/**
	 * Update a specific resource at the given url.
	 *
	 * @param $type
	 * @param array $data
	 * @return \stdClass
	 * @throws CachetException If the status code returned wasn't what we expected.
	 */
	public function update($type, $id, array $update) {
		try {
			$res = $this->client->put($type . '/' . $id, [
				'json' => $update
			]);

			if ($res->getStatusCode() !== 200) {
				throw new CachetException('Invalid response code!');
			}

			$json = $this->json($res);

			return $json->data;
		} catch (RequestException $e) {
			$this->handleException($e);
		}
	}

	/**
	 * Delete a specific resource at the given url.
	 *
	 * @param $type
	 * @param $id
	 * @return bool
	 * @throws CachetException If the status code returned wasn't what we expected.
	 */
	public function delete($type, $id) {
		try {
			$res = $this->client->delete($type . '/' . $id);

			if ($res->getStatusCode() !== 204) {
				throw new CachetException('Invalid response code!');
			}

			return true;
		} catch (RequestException $e) {
			$this->handleException($e);
		}
	}

	/**
	 * Convert a Guzzle Response to JSON.
	 *
	 * @param ResponseInterface $response
	 * @return \stdClass|array
	 */
	private function json(ResponseInterface $response) {
		return json_decode($response->getBody());
	}

	/**
	 * Handles exceptions for requests, specifically unauthorized access.
	 *
	 * @param RequestException $e
	 * @throws CachetAuthenticationException
	 */
	private function handleException(RequestException $e) {
		if ($e->hasResponse()) {
			// Unauthorized
			if ($e->getResponse()->getStatusCode() == 401) {
				throw new CachetAuthenticationException();
			}
		}

		// Push it up the stack.
		throw $e;
	}
}