<?php
App::uses('AppController', 'Controller');
/**
 * SubmissionFields Controller
 *
 * @property SubmissionField $SubmissionField
 */
class SubmissionFieldsController extends AppController {

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->SubmissionField->recursive = 0;
		$this->set('submissionFields', $this->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		$this->SubmissionField->id = $id;
		if (!$this->SubmissionField->exists()) {
			throw new NotFoundException(__('Invalid submission field'));
		}
		$this->set('submissionField', $this->SubmissionField->read(null, $id));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->SubmissionField->create();
			if ($this->SubmissionField->save($this->request->data)) {
				$this->Session->setFlash(__('The submission field has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The submission field could not be saved. Please, try again.'));
			}
		}
		$modelTypes = $this->SubmissionField->ModelType->find('list');
		$statuses = $this->SubmissionField->Status->find('list');
		$this->set(compact('modelTypes', 'statuses'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		$this->SubmissionField->id = $id;
		if (!$this->SubmissionField->exists()) {
			throw new NotFoundException(__('Invalid submission field'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->SubmissionField->save($this->request->data)) {
				$this->Session->setFlash(__('The submission field has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The submission field could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->SubmissionField->read(null, $id);
		}
		$modelTypes = $this->SubmissionField->ModelType->find('list');
		$statuses = $this->SubmissionField->Status->find('list');
		$this->set(compact('modelTypes', 'statuses'));
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
		$this->SubmissionField->id = $id;
		if (!$this->SubmissionField->exists()) {
			throw new NotFoundException(__('Invalid submission field'));
		}
		if ($this->SubmissionField->delete()) {
			$this->Session->setFlash(__('Submission field deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Submission field was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
}
