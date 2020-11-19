<?php
App::uses('AppController', 'Controller');
/**
 * ModelTypes Controller
 *
 * @property ModelType $ModelType
 */
class ModelTypesController extends AppController {

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->ModelType->recursive = 0;
		$this->set('modelTypes', $this->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		$this->ModelType->id = $id;
		if (!$this->ModelType->exists()) {
			throw new NotFoundException(__('Invalid model type'));
		}
		$this->set('modelType', $this->ModelType->read(null, $id));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->ModelType->create();
			if ($this->ModelType->save($this->request->data)) {
				$this->Session->setFlash(__('The model type has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The model type could not be saved. Please, try again.'));
			}
		}
		$statuses = $this->ModelType->Status->find('list');
		$this->set(compact('statuses'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		$this->ModelType->id = $id;
		if (!$this->ModelType->exists()) {
			throw new NotFoundException(__('Invalid model type'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->ModelType->save($this->request->data)) {
				$this->Session->setFlash(__('The model type has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The model type could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->ModelType->read(null, $id);
		}
		$statuses = $this->ModelType->Status->find('list');
		$this->set(compact('statuses'));
	}

/**
 * delete method
 *
 * @throws MethodNotAllowedException
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->ModelType->id = $id;
		if (!$this->ModelType->exists()) {
			throw new NotFoundException(__('Invalid model type'));
		}
		if ($this->ModelType->delete()) {
			$this->Session->setFlash(__('Model type deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Model type was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
	
	public function getActive() {
		return $this->ModelType->find('all', array('recursive' => 0, 'conditions' => array('Status.code' => 'active'), 'fields' => array('ModelType.id', 'ModelType.title'), 'order' => 'ModelType.title'));
	}
}
