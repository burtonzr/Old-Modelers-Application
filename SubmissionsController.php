<?php
App::uses('AppController', 'Controller');
/**
 * Submissions Controller
 *
 * @property Submission $Submission
 */
class SubmissionsController extends AppController {
/**
 * index method
 *
 * @return void
 */
	public function index() {
		//die("<pre>".print_r($this->params, true)."</pre>");
		if(!isset($this->params["named"]["modeltype"])) {
			$this->setAction("galleryHome");
		} else {
			$view_year = date("Y");
			if(isset($this->params["named"]["year"])) {
				$view_year = $this->params["named"]["year"];
			}
			$oldest = $this->Submission->find("first", array("conditions" => array("Status.code" => "active", "not" => array("Submission.approved" => null)), "fields" => array("Submission.approved"), "order" => array("Submission.approved ASC")));
			$modelTypeTitle = $this->Submission->ModelType->field("ModelType.title", array('ModelType.id' => $this->params["named"]["modeltype"]));
			//$this->Submission->recursive = 0;
			$options = array(
				'conditions' => array('Status.code' => 'active', 'ModelType.id' => $this->params["named"]["modeltype"]), 
				'order' => array('Submission.approved' => 'desc', 'Submission.created' => 'desc', 'Submission.modified' => 'desc', 'Submission.subject' => 'asc'), 
				'limit' => 20
			);
			$this->paginate = $options;
			$submissions = $this->paginate("Submission");
			//$submissions = $this->Submission->find("all", $options);
			$modelTypeTitle = $this->Submission->ModelType->field("ModelType.title", array('ModelType.id' => $this->params["named"]["modeltype"]));
			//$images = $this->Submission->Image->find("all", array("conditions" => array("Status.code" => "active")));
			$this->set(compact('submissions', 'modelTypeTitle', 'oldest', 'view_year', 'modelTypeTitle'));
		}
	}
	
	public function galleryHome() {
		$modelTypes = $this->Submission->ModelType->find('all', array('recursive' => 0, 'conditions' => array('Status.code' => 'active'), 'fields' => array('ModelType.id', 'ModelType.title', 'ModelType.code'), 'order' => 'ModelType.title'));
		//$this->Submission->find('all', array('conditions' => array('Status.code' => 'active', 'ModelType.id' => )));
		$this->set('modelTypes', $modelTypes);
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		$this->Submission->id = $id;
		if (!$this->Submission->exists()) {
			throw new NotFoundException(__('Invalid submission'));
		}
		$this->set('submission', $this->Submission->read(null, $id));
	}

/**
 * add method
 *
 * @return void
 */	
	public function add() {
		if(!isset($this->params["named"]["modeltype"])) {
			$this->redirect(array('action' => 'galleryHome'));
		} else {
			$model_type_id = intval($this->params["named"]["modeltype"]);
			if ($this->request->is('post')) {
				//$dataSource = $this->Submission->getDataSource();
				//$dataSource->begin();
				$fail = false;
				//Check for an "Other" category
				$originalSubmissionCategoryId = $this->request->data["Submission"]["submission_category_id"];
				if($this->request->data["Submission"]["submission_category_id"] === "0") {
					$this->Submission->SubmissionCategory->create();
					$this->request->data["SubmissionCategory"]["model_type_id"] = $model_type_id;
					$this->request->data["SubmissionCategory"]["status_id"] = $this->Submission->SubmissionCategory->Status->field("Status.id", array("Status.type" => "submission_categories", "Status.code" => "active"));
					$this->request->data["SubmissionCategory"]["code"] = strtolower(Inflector::slug($this->request->data["SubmissionCategory"]["title"]));
					if($this->Submission->SubmissionCategory->save($this->request->data)) {
						$this->request->data["Submission"]["submission_category_id"] = $this->Submission->SubmissionCategory->id;
					} else {
						$fail = true;
						$this->Session->setFlash(__('The new submission category could not be saved. Please, try again.'));
					}
				}
				//Check for an "Other" manufacturer
				$originalManufacturerId = $this->request->data["Submission"]["manufacturer_id"];
				if($this->request->data["Submission"]["manufacturer_id"] === "0") {
					$this->Submission->Manufacturer->create();
					$this->request->data["Manufacturer"]["status_id"] = $this->Submission->Manufacturer->Status->field("Status.id", array("Status.type" => "manufacturers", "Status.code" => "active"));
					if($this->Submission->Manufacturer->save($this->request->data)) {
						$this->request->data["Submission"]["manufacturer_id"] = $this->Submission->Manufacturer->id;
					} else {
						$fail = true;
						$this->Session->setFlash(__('The new manufacturer could not be saved. Please, try again.'));
					}
				}
				//Check for an "Other" scale
				$originalScaleId = $this->request->data["Submission"]["scale_id"];
				if($this->request->data["Submission"]["scale_id"] === "0") {
					$this->Submission->Scale->create();
					$this->request->data["Scale"]["model_type_id"] = $model_type_id;
					if($this->Submission->Scale->save($this->request->data)) {
						$this->request->data["Submission"]["scale_id"] = $this->Submission->Scale->id;
					} else {
						$fail = true;
						$this->Session->setFlash(__('The new scale could not be saved. Please, try again.'));
					}
				}
				//Check if existing user and if so, pull ID for saving
				$user_id = $this->Submission->User->field("User.id", array("User.email" => $this->request->data["User"]["email"]));
				if(!$user_id) {
					$this->Submission->User->create();
					$this->request->data["User"]["status_id"] = $this->Submission->User->Status->field("Status.id", array("Status.type" => "users", "Status.code" => "pending"));
					if($this->Submission->User->save($this->request->data)) {
						$user_id = $this->Submission->User->id;
					} else {
						$fail = true;
						$this->Session->setFlash(__('New user could not be saved. Please, try again.'));
					}
				}
				$this->request->data["Submission"]["user_id"] = $user_id;
				//And save the submission
				$this->Submission->create();
				/* NEED TO CLEANUP ANY HTML IN BODY */
				$this->request->data["Submission"]["model_type_id"] = $model_type_id;
				$this->request->data["Submission"]["status_id"] = $this->Submission->Status->field('Status.id', array('Status.type' => 'submissions', 'Status.code' => 'active'));
				if ($this->Submission->save($this->request->data)) {
					//$this->Session->setFlash(__('The submission has been saved'));
					//$this->redirect(array('action' => 'index'));
				} else {
					$fail = true;
					$this->Session->setFlash(__('The submission could not be saved. Please, try again.'));
				}
				//Save all the images
				//die("<pre>".print_r($this->request->data, true)."</pre>");
				$imgdir = Configure::read('Gallery.imgdir');
				$first_id = null;
				foreach($this->request->data["Image"]["file"] as $image) {
					if(intval($image["error"]) === 0) {
						//Check image type
						
						//Check file size
						
						//Check image dimensions
						
						//Check which directory to save to
						$dir = $imgdir.date('Y').DS.$this->Submission->ModelType->field('ModelType.code', array('ModelType.id' => $model_type_id)).DS.$this->Submission->SubmissionCategory->field('SubmissionCategory.code', array('SubmissionCategory.id' => $this->request->data["Submission"]["submission_category_id"])).DS;
						if(!is_dir($dir)) {
							if(!mkdir($dir, 0777, true)) {
								$fail = true;
								break;
							}
						}
						
						//Check for duplicate file name
						$filename = strtolower(str_replace(' ', '_', $image["name"]));
						$pathinfo = pathinfo($filename);
						$filename = $pathinfo["filename"];
						if(file_exists($dir.$filename.".".$pathinfo["extension"])) {
							$filename .= $pathinfo["filename"].$this->Submission->id;
						}
						$testname = $filename;
						while(file_exists($dir.$testname.$pathinfo["extension"])) {
							$testname = $pathinfo["filename"]."_".substr(MD5(microtime(true)), 0, rand(5, 15));
						}
						$filename = $testname;
						
						//Copy to directory
						move_uploaded_file($image["tmp_name"], $dir.$filename.".".$pathinfo["extension"]);

						//Add to data to save
						$imgData = array(
							"original_filename" => $image["name"],
							"storage_filename" => $filename.".".$pathinfo["extension"],
							"mime_type" => $image["type"],
							"filesize" => $image["size"],
							"submission_id" => $this->Submission->id,
							"status_id" => $this->Submission->Status->field("Status.id", array('Status.type' => 'images', 'code' => 'unverified'))
						);
						$this->Submission->Image->create();
						if(!$this->Submission->Image->save($imgData)) {
							$fail = true;
						} elseif(!$first_id) {
							$first_id = $this->Submission->Image->id;
						}
						//Create thumbnail
						require_once(APP . 'Vendor' . DS."phpthumb".DS."phpthumb.class.php");
						$phpThumb = new phpThumb();
						$phpThumb->src = $dir.$filename.".".$pathinfo["extension"];
						$phpThumb->h = 125;
						if($phpThumb->GenerateThumbnail()) {
							$phpThumb->RenderToFile($dir.$filename."_thumb.".$pathinfo["extension"]); 
						} else { 
							die('Failed: '.$phpThumb->error); 
						}
						unset($phpThumb);
					}
				}
				$this->request->data["Submission"]["main_image"] = $first_id;
				$this->Submission->save($this->request->data);
				//Revert IDs if there's a failure
				if($fail) {
					//$dataSource->rollback();
					$this->request->data["Submission"]["submission_category_id"] = $originalSubmissionCategoryId;
					$this->request->data["Submission"]["manufacturer_id"] = $originalManufacturerId;
					$this->request->data["Submission"]["scale_id"] = $originalScaleId;
				} else {
					//$dataSource->commit();
					$this->Session->setFlash(__('The submission has been saved and is awaiting Admin approval'));
					$this->redirect(array('action' => 'index', 'modeltype' => $model_type_id));
				}
			}
			$modelTypeTitle = $this->Submission->ModelType->field("ModelType.title", array('ModelType.id' => $model_type_id));
			$submissionCategories = array('' => 'Select '.strtolower($modelTypeTitle)." category...") + $this->Submission->SubmissionCategory->find('list', array('conditions' => array('SubmissionCategory.approved_yn' => 1, 'SubmissionCategory.model_type_id' => $model_type_id), 'order' => array('SubmissionCategory.title'), 'fields' => array('SubmissionCategory.id', 'SubmissionCategory.title'))) + array('0' => 'Other (specify)');
			$manufacturers = array('' => 'Select kit manufacturer...') + $this->Submission->Manufacturer->find('list', array('conditions' => array('Manufacturer.approved_yn' => 1), 'order' => array('Manufacturer.name'), 'fields' => array('Manufacturer.id', 'Manufacturer.name'))) + array('0' => 'Other (specify)');
			$scales = array('' => '') + $this->Submission->Scale->find('list', array('conditions' => array('Scale.model_type_id' => $model_type_id), 'order' => array('Scale.scale ASC'))) + array('0' => 'Other (specify)');
			//die("<pre>".print_r($scales, true)."</pre>");
			$this->set(compact('submissionCategories', 'manufacturers', 'scales', 'modelTypeTitle'));
		}
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		$this->Submission->id = $id;
		if (!$this->Submission->exists()) {
			throw new NotFoundException(__('Invalid submission'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Submission->save($this->request->data)) {
				$this->Session->setFlash(__('The submission has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The submission could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->Submission->read(null, $id);
		}
		$users = $this->Submission->User->find('list');
		$modelTypes = $this->Submission->ModelType->find('list');
		$submissionCategories = $this->Submission->SubmissionCategory->find('list');
		$manufacturers = $this->Submission->Manufacturer->find('list');
		$scales = $this->Submission->Scale->find('list');
		$statuses = $this->Submission->Status->find('list');
		$this->set(compact('users', 'modelTypes', 'submissionCategories', 'manufacturers', 'scales', 'statuses'));
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
		$this->Submission->id = $id;
		if (!$this->Submission->exists()) {
			throw new NotFoundException(__('Invalid submission'));
		}
		if ($this->Submission->delete()) {
			$this->Session->setFlash(__('Submission deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Submission was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
}
