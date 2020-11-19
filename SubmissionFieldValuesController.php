<?php
App::uses('AppController', 'Controller');
/**
 * SubmissionFieldValues Controller
 *
 * @property SubmissionFieldValue $SubmissionFieldValue
 */
class SubmissionFieldValuesController extends AppController {

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->SubmissionFieldValue->recursive = 0;
		$this->set('submissionFieldValues', $this->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		$this->SubmissionFieldValue->id = $id;
		if (!$this->SubmissionFieldValue->exists()) {
			throw new NotFoundException(__('Invalid submission field value'));
		}
		$this->set('submissionFieldValue', $this->SubmissionFieldValue->read(null, $id));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->SubmissionFieldValue->create();
			if ($this->SubmissionFieldValue->save($this->request->data)) {
				$this->Session->setFlash(__('The submission field value has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The submission field value could not be saved. Please, try again.'));
			}
		}
		$submissions = $this->SubmissionFieldValue->Submission->find('list');
		$submissionFields = $this->SubmissionFieldValue->SubmissionField->find('list');
		$this->set(compact('submissions', 'submissionFields'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		$this->SubmissionFieldValue->id = $id;
		if (!$this->SubmissionFieldValue->exists()) {
			throw new NotFoundException(__('Invalid submission field value'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->SubmissionFieldValue->save($this->request->data)) {
				$this->Session->setFlash(__('The submission field value has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The submission field value could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->SubmissionFieldValue->read(null, $id);
		}
		$submissions = $this->SubmissionFieldValue->Submission->find('list');
		$submissionFields = $this->SubmissionFieldValue->SubmissionField->find('list');
		$this->set(compact('submissions', 'submissionFields'));
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
		$this->SubmissionFieldValue->id = $id;
		if (!$this->SubmissionFieldValue->exists()) {
			throw new NotFoundException(__('Invalid submission field value'));
		}
		if ($this->SubmissionFieldValue->delete()) {
			$this->Session->setFlash(__('Submission field value deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Submission field value was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
}
