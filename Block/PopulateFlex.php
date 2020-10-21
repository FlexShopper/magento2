<?php


namespace FlexShopper\Payments\Block;

use Magento\Framework\View\Asset\Repository as AssetRepository;
use Magento\Framework\View\Element\Template;

class PopulateFlex extends \Magento\Framework\View\Element\Template
{
    /**
     * @var AssetRepository
     */
    private $assetRepository;
    private $request;

    public function __construct(
        AssetRepository $assetRepository,
        \Magento\Framework\App\RequestInterface $request,
        Template\Context $context,
        array $data = []
    ){
        parent::__construct($context, $data);
        $this->assetRepository = $assetRepository;
        $this->request = $request;
    }

    public function getFlexConfig() {
        $output['flexLogoImageUrl'] = $this->getViewFileUrl('FlexShopper_Payments::images/flex_logo.svg');

        return $output;
    }

    public function getViewFileUrl($fileId, array $params = [])
    {
        $params = array_merge(['_secure' => $this->request->isSecure()], $params);
        return $this->assetRepository->getUrlWithParams($fileId, $params);
    }

}
