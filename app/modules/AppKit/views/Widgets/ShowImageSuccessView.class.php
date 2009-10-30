<?php

class AppKit_Widgets_ShowImageSuccessView extends ICINGAAppKitBaseView
{
	public function executeImage(AgaviRequestDataHolder $rd)
	{
		// Get the model (hope the action has finally created the singleton) 
		$image = $this->getContext()->getModel('ImageFile', 'AppKit');
		
		if ($image->getImageFile()) {
			
			// Get the magick
			$response = $this->getContainer()->getResponse();
			
			// Setting some header upon the image type
			$response->clearHttpHeaders();
			$response->setHttpHeader('Content-Type', $image->getImageContentType(), true);
			$response->setHttpHeader('Content-Length', $image->getFileInfo()->getSize(), true);
			$response->setHttpHeader('Expires', gmdate('D, d M Y H:i:s \G\M\T', time() + 3600), true);
			$response->setHttpHeader('Cache-Control', 'public', true);
			$response->setHttpHeader('Age', 10, true);
			$response->setHttpHeader('Pragma', '', true);
			
			
			// Return the resource, because the AgaviResponse understand fpassthrough
			return $image->getImageResource();

		}
		
		// HMPF return nothing!
		return null;
	}
}

?>