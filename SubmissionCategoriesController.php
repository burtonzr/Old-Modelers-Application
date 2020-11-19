<?php
App::uses('AppController', 'Controller');
/**
 * SubmissionCategories Controller
 *
 * @property SubmissionCategory $SubmissionCategory
 */
class SubmissionCategoriesController extends AppController {

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->SubmissionCategory->recursive = 0;
		$this->set('submissionCategories', $this->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		$this->SubmissionCategory->id = $id;
		if (!$this->SubmissionCategory->exists()) {
			throw new NotFoundException(__('Invalid submission category'));
		}
		$this->set('submissionCategory', $this->SubmissionCategory->read(null, $id));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->SubmissionCategory->create();
			if ($this->SubmissionCategory->save($this->request->data)) {
				$this->Session->setFlash(__('The submission category has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The submission category could not be saved. Please, try again.'));
			}
		}
		$parentSubmissionCategories = $this->SubmissionCategory->ParentSubmissionCategory->find('list');
		$modelTypes = $this->SubmissionCategory->ModelType->find('list');
		$statuses = $this->SubmissionCategory->Status->find('list');
		$this->set(compact('parentSubmissionCategories', 'modelTypes', 'statuses'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		$this->SubmissionCategory->id = $id;
		if (!$this->SubmissionCategory->exists()) {
			throw new NotFoundException(__('Invalid submission category'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->SubmissionCategory->save($this->request->data)) {
				$this->Session->setFlash(__('The submission category has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The submission category could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->SubmissionCategory->read(null, $id);
		}
		$parentSubmissionCategories = $this->SubmissionCategory->ParentSubmissionCategory->find('list');
		$modelTypes = $this->SubmissionCategory->ModelType->find('list');
		$statuses = $this->SubmissionCategory->Status->find('list');
		$this->set(compact('parentSubmissionCategories', 'modelTypes', 'statuses'));
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
		$this->SubmissionCategory->id = $id;
		if (!$this->SubmissionCategory->exists()) {
			throw new NotFoundException(__('Invalid submission category'));
		}
		if ($this->SubmissionCategory->delete()) {
			$this->Session->setFlash(__('Submission category deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Submission category was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
}
